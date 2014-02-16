<?php
namespace PageBuilder\View\Helper;

use Gedmo\Sluggable\Util\Urlizer;
use PageBuilder\BaseWidget;
use PageBuilder\Entity\Page;
use PageBuilder\Exception\RuntimeException;
use PageBuilder\View\TagAttributes;
use PageBuilder\WidgetData;
use PageBuilder\WidgetFactory;
use SynergyCommon\Entity\AbstractEntity;
use SynergyCommon\Entity\BasePage;
use Zend\Log\Formatter\FormatterInterface;
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
class PageBuilder
    extends AbstractHelper
    implements ServiceLocatorAwareInterface
{
    const MAIN_CONTENT   = 'main';
    const FLASH_MESSAGES = 'flash';

    const LAYOUT_MENU         = 'menu';
    const LAYOUT_USER_DEFINED = 'component';
    const LAYOUT_WIDGET       = 'widget';
    const LAYOUT_BREADCRUMB   = 'breadcrumb';

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
     * @param AbstractEntity $activeTheme
     *
     * @return $this
     */
    public function init(BasePage $page, Navigation $menuTree, AbstractEntity $activeTheme = null)
    {
        if ($this->getOptions()->getEnabled()) {

            $this->_menuTree = $menuTree;
            $siteTheme       = $activeTheme ? (string)$activeTheme : 'default';

            $layout = null;
            /** @var $theme \PageBuilder\Entity\Join\PageTheme */

            if (method_exists($page, 'getPageThemes')) {
                foreach ($page->getPageThemes() as $theme) {
                    if ($theme->getIsActive() and $theme->getThemeId()->getSlug() == $siteTheme) {
                        $this->_activeTheme = $theme;
                        $layout             = $theme->getLayout();
                        break;
                    }
                }
            }

            //try to get the layout from the page template
            /** @var $template \PageBuilder\Entity\Template */

            /** @var $page \PageBuilder\Entity\Page */
            if (!$layout and $template = $page->getTemplate()) {
                //Use customized page template if set, otherwise use global template
                $layout = $template->getLayout() ? : array();
            }

            /** @var $parent \PageBuilder\Entity\Page */
            //try to ge the layout from the parent if it exists
            if (!$layout and $parent = $page->getParent()) {
                /** @var $temp \PageBuilder\Entity\Template */
                if ($temp = $parent->getTemplate()) {
                    $layout = $temp->getLayout();
                }
            }

            foreach ($layout as &$template) {
                $sectionAttr               = isset($template['tagAttributes']) ? $template['tagAttributes'] : array();
                $template['tagAttributes'] = new TagAttributes($sectionAttr);
                if (isset($template['items'])) {
                    foreach ($template['items'] as &$row) {
                        $rowAttr              = isset($row['tagAttributes']) ? $row['tagAttributes'] : array();
                        $row['tagAttributes'] = new TagAttributes($rowAttr);
                        if (isset($row['rowItems'])) {
                            foreach ($row['rowItems'] as &$col) {
                                if (isset($col['item'])) {
                                    foreach ($col['item'] as $index => $item) {
                                        list($itemType, $itemId) = explode('-', $item['name']);
                                        $attr                  = isset($item['tagAttributes']) ? $item['tagAttributes']
                                            : array();
                                        $item['tagAttributes'] = new TagAttributes($attr);
                                        $col['item'][$index]   = $this->getItem(
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

            $this->_layout = $layout;
        }

        return $this;
    }

    public function __invoke($content = '')
    {
        $html = array();

        if ($layout = $this->getLayout()) {
            $this->_mainContent = $content;

            /** @var $microDataHelper  \PageBuilder\View\Helper\MicroData */
            $microDataHelper = $this->_serviceManager
                ->get('viewhelpermanager')->get('microdata'); //  getView()->microData();

            /** @var $template['tagAttributes'] \PageBuilder\View\TagAttributes */
            foreach ($layout as $section => $template) {
                /** @var $templateAttr \PageBuilder\View\TagAttributes */
                $templateAttr   = $template['tagAttributes'];
                $sectionWrapper = $templateAttr->getWrapper();
                $templateAttr->addClass($section . '-section');

                if ($sectionWrapper) {
                    $html [] = '<' . $sectionWrapper . $templateAttr->formatClass()
                        . $templateAttr->formatId() . $templateAttr->formatAttr();
                    switch ($section) {
                        case 'header':
                            $microData = $microDataHelper->scopeAndProperty('WebPage', 'WPHeader');
                            break;
                        case 'footer':
                            $microData = $microDataHelper->scopeAndProperty('WebPage', 'WPFooter');
                            break;
                        default:
                            $microData = '';
                            break;
                    }

                    $html [] = $microData . '>';
                }

                list($top, $bottom) = $this->getTopBottomContainers($template['tagAttributes']);
                $html [] = $top;

                if (isset($template['items'])) {

                    foreach ($template['items'] as $row) {
                        if (isset($row['rowItems'])) {
                            /** @var $attr \PageBuilder\View\TagAttributes */
                            $attr = $row['tagAttributes'];

                            list($rowTop, $rowBottom) = $this->getTopBottomContainers($attr);

                            if ($rowWrapper = $attr->getWrapper()) {
                                $html [] = '<' . $rowWrapper . $attr->formatClass() . '>';
                            }

                            $html [] = $rowTop;

                            foreach ($row['rowItems'] as $col) {

                                if (count($row['rowItems']) > 1) {
                                    $html [] = '<div class="' . $col['class'] . '">'; //bootstrap column
                                }

                                /** @var $item \PageBuilder\WidgetData */
                                foreach ($col['item'] as $item) {
                                    if ($wrapper = $item->getAttributes()->getWrapper()) {
                                        $html[] = sprintf(
                                            '<%s %s %s %s>',
                                            $wrapper,
                                            $item->getAttributes()->formatClass(),
                                            $item->getAttributes()->formatId(),
                                            $item->getAttributes()->formatAttr()
                                        );
                                    }

                                    $html[] = str_replace(
                                        array(
                                             '{{' . self::MAIN_CONTENT . '}}'
                                        ),
                                        array(
                                             $content
                                        ),
                                        is_string($item->getData()) ? $item->getData() : $item->getData()->render()
                                    );

                                    if ($wrapper) {
                                        $html[] = '</' . $item->getAttributes()->getWrapper() . '>';
                                    }
                                }

                                if (count($row['rowItems']) > 1) {
                                    $html [] = '</div>';
                                }
                            }


                            $html [] = $rowBottom;

                            if ($rowWrapper) {
                                $html [] = '</' . $rowWrapper . '>';
                            }
                        }
                    }

                    $html [] = $bottom;

                    if ($sectionWrapper) {
                        $html [] = '</' . $sectionWrapper . '>';
                    }
                }
            }

            return implode('', $html);
        } else {
            return $content;
        }
    }

    /**
     * @param               $itemType
     * @param               $id
     * @param TagAttributes $attr
     *
     * @return object
     * @throws RuntimeException
     */
    protected function getItem($itemType, $id, TagAttributes $attr)
    {
        $data = '';

        switch ($itemType) {

            case self::LAYOUT_WIDGET:
                try {
                    /** @var $data \PageBuilder\BaseWidget */
                    $widgetName = $id . WidgetFactory::WIDGET_SUFFIX;
                    $options    = $attr->getOptions();

                    if (isset($options['shared']) and !$options['shared']) {
                        $this->getServiceManager()->setShared($widgetName, false);
                    }
                    $data = $this->getServiceManager()->get($widgetName);
                    $attr->addClass($data->getId());
                    $data->setAttributes($attr);
                } catch (ServiceNotFoundException $e) {
                    $data = '';
                }

                break;
            case self::LAYOUT_USER_DEFINED:
                /** @var $componentModel \PageBuilder\Model\ComponentModel */
                $componentModel = $this->getServiceManager()->get('pagebuilder\model\component');

                /** @var $component \PageBuilder\Entity\Component */
                if ($component = $componentModel->findObject($id)) {
                    $data     = $component->getContent();
                    $comId    = "data-id='{$itemType}-{$id}'";
                    $cssClass = trim("{$itemType} {$component->getCssClass()}");
                    $attr->addClass($cssClass)
                        ->addClass($component->getCssId())
                        ->addAttr($comId)
                        ->addAttr("id='{$component->getCssId()}'");

                    //Apply replaces to the output
                    $replacements = $this->getOptions()->getReplacements();
                    $data         = str_replace(array_keys($replacements), array_values($replacements), $data);

                    //apply formats to the data
                    $data = $this->_applyFormats($data);
                }

                break;
            default:
                $message = 'Layout itemType ' . $itemType . ' not found';
                if ($this->_serviceManager->has('logger')) {
                    $this->_serviceManager->get('logger')->err($message);
                } else {
                    throw new RuntimeException($message);
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
    protected function _applyFormats($data)
    {
        if ($this->getOptions()->getOutputFormatters()) {
            /** @var $formatter \PageBuilder\FormatterInterface */
            foreach ($this->getOptions()->getOutputFormatters() as $formatter) {
                if ($formatter instanceof FormatterInterface) {
                    $data = $formatter->format($data);
                }
            }
        }

        return $data;
    }

    public static function getSections()
    {
        return self::$sections;
    }

    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_pluginManager = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->_pluginManager;
    }

    protected function getTopBottomContainers(TagAttributes $attr)
    {
        $top = $bottom = '';
        if ($containerClass = $attr->getContainer()) {
            $top    = '<div class="' . $containerClass . '">';
            $bottom = '</div>';

            if ($container2 = $attr->getContainer2()) {
                $top .= '<div class="' . $container2 . '">';
                $bottom .= '</div>';
            }
        }

        return array($top, $bottom);
    }

    public function setServiceManager($serviceManager)
    {
        $this->_serviceManager = $serviceManager;

        return $this;
    }

    public function getServiceManager()
    {
        if (!$this->_serviceManager) {
            $this->_serviceManager = $this->_pluginManager->getServiceLocator();
        }

        return $this->_serviceManager;
    }

    /**
     * @param \PageBuilder\View\Helper\Config\PageBuilderConfig $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * @return \PageBuilder\View\Helper\Config\PageBuilderConfig
     */
    public function getOptions()
    {
        return $this->_options;
    }

}