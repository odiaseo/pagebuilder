<?php
namespace PageBuilder\View\Helper;

use PageBuilder\Exception\RuntimeException;
use PageBuilder\FormatterInterface;
use PageBuilder\Model\PageThemeModel;
use PageBuilder\Service\LayoutService;
use PageBuilder\View\TagAttributes;
use PageBuilder\WidgetData;
use PageBuilder\WidgetFactory;
use SynergyCommon\Entity\AbstractEntity;
use SynergyCommon\Entity\BasePage;
use Zend\Filter\FilterInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Navigation;

/**
 * Class PageBuilder
 * Gets the current active Page and retrieves the page details
 *
 * @package PageBuilder\View\Helper
 */
class PageBuilder extends AbstractHelper implements ServiceLocatorAwareInterface {
	const SHARE_KEY = 'shared';
	const MAIN_CONTENT = 'main';
	const FLASH_MESSAGES = 'flash';

	const LAYOUT_MENU = 'menu';
	const LAYOUT_USER_DEFINED = 'component';
	const LAYOUT_WIDGET = 'widget';
	const LAYOUT_BREADCRUMB = 'breadcrumb';

	private $_activeTheme;
	private $_mainContent;
	private $_menuTree;
	private $_layout = array();
	/** @var \Zend\View\HelperPluginManager */
	protected $_pluginManager;
	/** @var \Zend\ServiceManager\ServiceManager */
	protected $_serviceManager;

	/** @var Config\PageBuilderConfig */
	protected $_options;
	/** @var  \PageBuilder\Entity\Theme */
	private $activeTheme;

	public static $sections
		= array(
			'top'    => 'Top Bar',
			'header' => 'Header',
			'menu'   => 'Menu',
			'body'   => 'Body',
			'footer' => 'Footer'
		);

	/**
	 * Prepare page data and widgets
	 * Get the active site theme and find a layout for the the current page that matches the site theme
	 * If not layout is found, use the template assigned to the page if set
	 *
	 * @param BasePage       $page
	 * @param Navigation     $menuTree
	 * @param AbstractEntity $activeSiteTheme
	 *
	 * @return $this
	 */
	public function init( BasePage $page, Navigation $menuTree = null, AbstractEntity $activeSiteTheme = null ) {

		if ( $this->getOptions()->getEnabled() ) {
			/** @var PageThemeModel $themeModel */
			/** @var LayoutService $layoutService */
			$themeModel    = $this->getServiceManager()->get( 'pagebuilder\model\page-theme' );
			$layoutService = $this->getServiceManager()->get( 'pagebuilder\service\layout' );
			$siteTheme     = $activeSiteTheme ? (string) $activeSiteTheme : 'default';
			$activeTheme   = $themeModel->getActivePageThemeForSite( $page->getId(), $siteTheme );

			if ( $activeTheme ) {
				$layout = $activeTheme->getLayout();
			} else {
				$layout = $layoutService->resolvePageLayout( $page, $siteTheme );
			}

			$this->_menuTree   = $menuTree;
			$this->activeTheme = $activeTheme;

			foreach ( $layout as $index => &$template ) {
				if ( array_key_exists( 'status', $template ) and empty( $template['status'] ) ) {
					unset( $layout[ $index ] );
				} else {
					$sectionAttr               = isset( $template['tagAttributes'] ) ? $template['tagAttributes']
						: array();
					$template['tagAttributes'] = new TagAttributes( $sectionAttr );
					if ( isset( $template['items'] ) ) {
						foreach ( $template['items'] as &$row ) {
							$rowAttr              = isset( $row['tagAttributes'] ) ? $row['tagAttributes'] : array();
							$row['tagAttributes'] = new TagAttributes( $rowAttr );
							if ( isset( $row['rowItems'] ) ) {
								foreach ( $row['rowItems'] as &$col ) {
									$colAttr              = isset( $col['tagAttributes'] ) ? $col['tagAttributes']
										: array();
									$col['tagAttributes'] = new TagAttributes( $colAttr );
									if ( isset( $col['item'] ) ) {
										foreach ( $col['item'] as $index => $item ) {
											list( $itemType, $itemId ) = explode( '-', $item['name'] );
											$attr                  = isset( $item['tagAttributes'] )
												? $item['tagAttributes']
												: array();
											$item['tagAttributes'] = new TagAttributes( $attr );
											$col['item'][ $index ] = $this->getItem(
												$itemType, $itemId, $item['tagAttributes']
											);
										}
									} else {
										$col['item'] = array();
									}
								}
							}
						}
					}
				}
			}

			$this->_layout = $layout;
		}

		return $this;
	}

	public function __invoke( $content = '' ) {
		$html = array( '<a id="top"></a>' );

		try {
			if ( $layout = $this->getLayout() ) {
				$this->_mainContent = $content;

				/** @var $template ['tagAttributes'] \PageBuilder\View\TagAttributes */
				foreach ( $layout as $section => $template ) {
					/** @var $templateAttr \PageBuilder\View\TagAttributes */
					$templateAttr = $template['tagAttributes'];
					$templateAttr->addClass( $section . '-section' );
					list( $top, $bottom ) = $this->getTopBottomContainers( $template['tagAttributes'], $section );

					$html [] = $top;

					if ( isset( $template['items'] ) ) {

						foreach ( $template['items'] as $row ) {
							if ( isset( $row['rowItems'] ) ) {
								list( $rowTop, $rowBottom ) = $this->getTopBottomContainers( $row['tagAttributes'] );
								$html [] = $rowTop;

								foreach ( $row['rowItems'] as $col ) {

									/** @var $colAttr \PageBuilder\View\TagAttributes */
									$colAttr = $col['tagAttributes'];

									if ( count( $row['rowItems'] ) > 1 ) {
										$colAttr->setClass( $col['class'] );
									}
									list( $colTop, $colBottom ) = $this->getTopBottomContainers( $colAttr );

									$html [] = $colTop;

									/** @var $item \PageBuilder\WidgetData */
									foreach ( $col['item'] as $item ) {
										list( $itemTop, $itemBottom ) = $this->getTopBottomContainers(
											$item->getAttributes(), null, count( $row['rowItems'] )
										);
										$html[] = $itemTop;
										$html[] = str_replace(
											array( '{{' . self::MAIN_CONTENT . '}}' ), array( $content ),
											is_string( $item->getData() ) ? $item->getData()
												: $item->getData()->render()
										);
										$html[] = $itemBottom;
									}

									$html[] = $colBottom;
								}
								$html [] = $rowBottom;
							}
						}
						$html [] = $bottom;
					}
				}

				$html = array_filter( $html );
				$html = implode( '', $html );

				if ( $this->activeTheme && $wrapperClass = $this->activeTheme->getWrapperClass() ) {
					$html = sprintf( '<div class="%s">%s</div>', $wrapperClass, $html );
				}

				if ( $alias = $this->_options->getFilter() ) {
					$filter = $this->getServiceManager()->get( $alias );

					if ( $filter instanceof FilterInterface ) {
						$html = $filter->filter( $html );
					}
				}

				return $html;
			} else {
				return $content;
			}
		} catch ( \Exception $e ) {
			$this->getServiceManager()->get( 'logger' )->logException( $e );
		}

		return $content;
	}

	/**
	 * @param               $itemType
	 * @param               $id
	 * @param TagAttributes $attr
	 *
	 * @return object
	 * @throws RuntimeException
	 */
	protected function getItem( $itemType, $id, TagAttributes $attr ) {
		$data = '';

		switch ( $itemType ) {

			case self::LAYOUT_WIDGET:
				try {
					/** @var $data \PageBuilder\BaseWidget */
					$widgetName = $id . WidgetFactory::WIDGET_SUFFIX;
					$options    = $attr->getOptions();

					if ( ! $this->isShared( $options ) ) {
						$this->getServiceManager()->setShared( $widgetName, false );
					}

					if ( array_key_exists( 'shared', $options ) and empty( $options['shared'] ) ) {
						$this->getServiceManager()->setShared( $widgetName, false );
					}

					$data = $this->getServiceManager()->get( $widgetName );
					$attr->addClass( $data->getId() );
					$data->setAttributes( $attr );
				} catch ( ServiceNotFoundException $e ) {
					$data = '';
				}

				break;
			case self::LAYOUT_USER_DEFINED:
				/** @var $componentModel \PageBuilder\Model\ComponentModel */
				$componentModel = $this->getServiceManager()->get( 'pagebuilder\model\component' );

				/** @var $component \PageBuilder\Entity\Component */
				if ( $component = $componentModel->findOneTranslatedBy( array( 'id' => $id ) ) ) {
					$data     = $this->transform( $component->getContent() );
					$comId    = "data-id='{$itemType}-{$id}'";
					$cssClass = trim( "{$itemType} {$component->getCssClass()}" );
					$attr->addClass( $cssClass )
					     ->addClass( $component->getCssId() )
					     ->addAttr( $comId )
					     ->addAttr( "id='{$component->getCssId()}'" );

					//Apply replaces to the output
					$replacements = $this->getOptions()->getReplacements();
					$data         = str_replace( array_keys( $replacements ), array_values( $replacements ), $data );

					//apply formats to the data
					$data = $this->_applyFormats( $data );
				}

				break;
			default:
				$message = 'Layout itemType ' . $itemType . ' not found';
				if ( $this->_serviceManager->has( 'logger' ) ) {
					$this->_serviceManager->get( 'logger' )->err( $message );
				} else {
					throw new RuntimeException( $message );
				}
		}

		return new WidgetData(
			array(
				'data'       => $data,
				'attributes' => $attr,
			)
		);
	}

	/**
	 * Apply formatter using the formatters in the order they were defined
	 *
	 * @param $data
	 *
	 * @return string
	 */
	protected function _applyFormats( $data ) {
		if ( $this->getOptions()->getOutputFormatters() ) {

			foreach ( $this->getOptions()->getOutputFormatters() as $formatter ) {
				if ( $formatter instanceof FormatterInterface ) {
					/** @var $formatter \PageBuilder\FormatterInterface */
					$data = $formatter->format( $data );
				} elseif ( is_callable( $formatter ) ) {
					$data = $formatter( $data, $this->_serviceManager );
				}
			}
		}

		return $data;
	}

	public static function getSections() {
		return self::$sections;
	}

	public function getLayout() {
		return $this->_layout;
	}

	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 *
	 * @return $this
	 */
	public function setServiceLocator( ServiceLocatorInterface $serviceLocator ) {
		$this->_pluginManager = $serviceLocator;

		return $this;
	}

	/**
	 * Get service locator
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator() {
		return $this->_pluginManager;
	}

	/**
	 * @param TagAttributes $attr
	 * @param TagAttributes $attr
	 * @param string        $section
	 *
	 * @return array
	 */
	protected function getTopBottomContainers( TagAttributes $attr, $section = '' ) {
		$top = $bottom = '';
		if ( $wrapper = $attr->getWrapper() ) {
			/** @var $microDataHelper  \PageBuilder\View\Helper\MicroData */
			$microDataHelper = $this->_pluginManager->get( 'microdata' );

			switch ( $section ) {
				case 'header':
					$microData = $microDataHelper->scopeAndProperty( 'WebPage', 'WPHeader' );
					break;
				case 'footer':
					$microData = $microDataHelper->scopeAndProperty( 'WebPage', 'WPFooter' );
					break;
				default:
					$microData = '';
					break;
			}

			$variables = array(
				trim( $wrapper ),
				$this->transform( trim( $attr->formatClass() ) ),
				trim( $attr->formatId() ),
				trim( $attr->formatAttr() ),
				trim( $microData )
			);

			$variables = array_filter( $variables );

			$top .= sprintf( '<%s>', implode( ' ', $variables ) );
		}

		if ( $containerClass = $attr->getContainer() ) {
			$top .= '<div class="' . $this->transform( $containerClass ) . '">';
			$bottom .= '</div>';

			if ( $container2 = $attr->getContainer2() ) {
				$top .= '<div class="' . $this->transform( $container2 ) . '">';
				$bottom .= '</div>';
			}
		}

		//main wrapper close
		if ( $wrapper ) {
			$bottom .= '</' . $wrapper . '>';
		}

		return array( $top, $bottom );
	}

	public function setServiceManager( $serviceManager ) {
		$this->_serviceManager = $serviceManager;

		return $this;
	}

	public function getServiceManager() {
		if ( ! $this->_serviceManager ) {
			$this->_serviceManager = $this->_pluginManager->getServiceLocator();
		}

		return $this->_serviceManager;
	}

	/**
	 * @param \PageBuilder\View\Helper\Config\PageBuilderConfig $options
	 */
	public function setOptions( $options ) {
		$this->_options = $options;
	}

	/**
	 * @return \PageBuilder\View\Helper\Config\PageBuilderConfig
	 */
	public function getOptions() {
		return $this->_options;
	}

	/**
	 * @param $options
	 *
	 * @return bool
	 */
	private function isShared( $options ) {
		if ( ! array_key_exists( self::SHARE_KEY, $options ) ) {
			return true;
		}

		if ( is_bool( $options[ self::SHARE_KEY ] ) ) {
			return $options[ self::SHARE_KEY ];
		}
		if ( is_numeric( $options[ self::SHARE_KEY ] ) ) {
			$shared = $options[ self::SHARE_KEY ] * 1;

			return ( $shared <= 0 ) ? false : true;
		}

		if ( $options[ self::SHARE_KEY ] == 'true' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $class
	 *
	 * @return mixed
	 */
	protected function transform( $class ) {
		if ( $this->_options->getBootstrapVersion() > 2 ) {
			foreach ( $this->_options->getCssClassmap() as $search => $replace ) {
				$pattern = '/' . $search . '/i';
				if ( preg_match( $pattern, $class, $matches ) ) {
					$done = str_replace( $search, $replace, $class );

					return $done;
				}
			}
		}

		return $class;
	}
}
