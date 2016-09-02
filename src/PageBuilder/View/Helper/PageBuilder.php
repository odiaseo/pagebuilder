<?php
namespace PageBuilder\View\Helper;

use PageBuilder\Entity\Site;
use PageBuilder\Exception\RuntimeException;
use PageBuilder\FormatterInterface;
use PageBuilder\Model\PageModel;
use PageBuilder\Model\PageTemplateModel;
use PageBuilder\Service\LayoutService;
use PageBuilder\View\TagAttributes;
use PageBuilder\WidgetData;
use PageBuilder\WidgetFactory;
use SynergyCommon\Entity\AbstractEntity;
use SynergyCommon\View\Helper\MicroData;
use Zend\Filter\FilterInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Navigation;

/**
 * Class PageBuilder
 * Gets the current active Page and retrieves the page details
 *
 * @package PageBuilder\View\Helper
 */
class PageBuilder extends AbstractHelper
{
    const SHARE_KEY = 'shared';
    const MAIN_CONTENT = 'main';
    const FLASH_MESSAGES = 'flash';

    const LAYOUT_MENU = 'menu';
    const LAYOUT_USER_DEFINED = 'component';
    const LAYOUT_WIDGET = 'widget';
    const LAYOUT_BREADCRUMB = 'breadcrumb';

    private $mainContent;

    private $menuTree;

    private $layout = [];

    /** @var \Zend\View\HelperPluginManager */
    protected $pluginManager;

    /** @var \Zend\ServiceManager\ServiceManager */
    protected $serviceManager;

    /** @var Config\PageBuilderConfig */
    protected $options;

    /** @var  \PageBuilder\Entity\Theme */
    private $activeTheme;

    public static $sections
        = [
            'top'    => 'Top Bar',
            'header' => 'Header',
            'menu'   => 'Menu',
            'body'   => 'Body',
            'footer' => 'Footer',
        ];

    /**
     * Prepare page data and widgets
     * Get the active site theme and find a layout for the the current page that matches the site theme
     * If not layout is found, use the template assigned to the page if set
     *
     * @param     int $pageId
     * @param Navigation $menuTree
     * @param AbstractEntity $activeSiteTheme
     *
     * @return $this
     */
    public function init($pageId, Navigation $menuTree = null, AbstractEntity $activeSiteTheme = null)
    {

        if ($this->getOptions()->getEnabled()) {
            /** @var PageTemplateModel $templateModel */
            /** @var PageModel $pageModel */
            /** @var LayoutService $layoutService */
            /** @var Site $site */
            $templateModel  = $this->getServiceLocator()->get('pagebuilder\model\pageTemplate');
            $layoutService  = $this->getServiceLocator()->get('pagebuilder\service\layout');
            $site           = $this->getServiceLocator()->get('active\site');
            $siteThemeId    = $activeSiteTheme ? $activeSiteTheme->getId() : 'default';
            $pageTemplate   = $templateModel->getActivePageThemeForSite($pageId, $siteThemeId, $site->getId());
            $this->menuTree = $menuTree;

            if ($pageTemplate) {
                $layout            = $pageTemplate->getTemplate()->getLayout();
                $this->activeTheme = $pageTemplate->getTemplate()->getTheme();
            }

            if (empty($layout)) {
                //get layout from the page root from the tree
                $layout = $layoutService->resolvePageLayout($pageId);
            }

            if (empty($this->activeTheme)) {
                $this->activeTheme = $activeSiteTheme;
            }

            foreach ($layout as $index => &$template) {
                if (array_key_exists('status', $template) and empty($template['status'])) {
                    unset($layout[$index]);
                } else {
                    $sectionAttr = isset($template['tagAttributes']) ? $template['tagAttributes'] : [];
                    $secAttrObj  = new TagAttributes($sectionAttr);

                    if ($secAttrObj->getActive()) {
                        $template['tagAttributes'] = $secAttrObj;
                        if (isset($template['items'])) {
                            foreach ($template['items'] as &$row) {
                                $rowAttr              = isset($row['tagAttributes']) ? $row['tagAttributes'] : [];
                                $row['tagAttributes'] = new TagAttributes($rowAttr);
                                if (isset($row['rowItems'])) {
                                    foreach ($row['rowItems'] as &$col) {
                                        $colAttr              = isset($col['tagAttributes']) ? $col['tagAttributes']
                                            : [];
                                        $col['tagAttributes'] = new TagAttributes($colAttr);
                                        if (isset($col['item'])) {
                                            foreach ($col['item'] as $index => $item) {
                                                list($itemType, $itemId) = explode('-', $item['name']);
                                                $attr                  = isset($item['tagAttributes'])
                                                    ? $item['tagAttributes']
                                                    : [];
                                                $item['tagAttributes'] = new TagAttributes($attr);
                                                $col['item'][$index]   = $this->getItem(
                                                    $itemType, $itemId, $item['tagAttributes']
                                                );
                                            }
                                        } else {
                                            $col['item'] = [];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $this->layout = $layout;
        }

        return $this;
    }

    public function __invoke($content = '')
    {
        $html = ['<a id="top"></a>'];

        try {
            if ($layout = $this->getLayout()) {
                $this->mainContent = $content;

                /** @var $template ['tagAttributes'] \PageBuilder\View\TagAttributes */
                foreach ($layout as $section => $template) {
                    /** @var $templateAttr \PageBuilder\View\TagAttributes */
                    $templateAttr = $template['tagAttributes'];
                    $templateAttr->addClass($section . '-section');
                    list($top, $bottom) = $this->getTopBottomContainers($template['tagAttributes'], $section);

                    $html [] = $top;

                    if (isset($template['items'])) {

                        foreach ($template['items'] as $row) {
                            if (isset($row['rowItems'])) {
                                list($rowTop, $rowBottom) = $this->getTopBottomContainers($row['tagAttributes']);
                                $html [] = $rowTop;

                                foreach ($row['rowItems'] as $col) {

                                    /** @var $colAttr \PageBuilder\View\TagAttributes */
                                    $colAttr = $col['tagAttributes'];

                                    if (count($row['rowItems']) > 1) {
                                        $colAttr->addClass($col['class']);
                                    }
                                    list($colTop, $colBottom) = $this->getTopBottomContainers($colAttr);

                                    $html [] = $colTop;

                                    /** @var $item \PageBuilder\WidgetData */
                                    foreach ($col['item'] as $item) {
                                        list($itemTop, $itemBottom) = $this->getTopBottomContainers(
                                            $item->getAttributes(), null
                                        );
                                        $html[] = $itemTop;
                                        try {
                                            $itemData = $item->getData();
                                            $mainData = str_replace(
                                                ['{{' . self::MAIN_CONTENT . '}}'], [$content],
                                                is_string($itemData) ? $itemData : $itemData->render()
                                            );
                                        } catch (\Exception $exception) {
                                            $this->getServiceLocator()->get('logger')->logException($exception);
                                            $mainData = '';
                                        }

                                        $html[] = $mainData;
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

                $html = array_filter($html);
                $html = implode('', $html);

                if ($this->activeTheme) {
                    $cssId        = $this->activeTheme->getWrapper();
                    $wrapperClass = $this->activeTheme->getWrapperClass();

                    if ($cssId or $wrapperClass) {
                        $wrapperClass = $wrapperClass ?: 'wrapper';
                        $cssId        = $cssId ?: 'pageTop';
                        $html         = sprintf('<div class="%s" id="%s">%s</div>', $wrapperClass, $cssId, $html);
                    }
                }

                if ($alias = $this->options->getFilter()) {
                    $filter = $this->getServiceLocator()->get($alias);

                    if ($filter instanceof FilterInterface) {
                        $html = $filter->filter($html);
                    }
                }

                return $html;
            } else {
                return $content;
            }
        } catch (\Exception $e) {
            $this->getServiceLocator()->get('logger')->logException($e);
        }

        return $content;
    }

    /**
     * @param               $itemType
     * @param               $itemId
     * @param TagAttributes $attr
     *
     * @return object
     * @throws RuntimeException
     */
    protected function getItem($itemType, $itemId, TagAttributes $attr)
    {
        $data = '';

        switch ($itemType) {

            case self::LAYOUT_WIDGET:
                try {
                    /** @var $data \PageBuilder\BaseWidget */
                    $widgetName = $itemId . WidgetFactory::WIDGET_SUFFIX;
                    $options    = $attr->getOptions();

                    if (!$this->isShared($options)) {
                        $this->getServiceLocator()->setShared($widgetName, false);
                    }

                    if (array_key_exists('shared', $options) and empty($options['shared'])) {
                        $this->getServiceLocator()->setShared($widgetName, false);
                    }

                    $data = $this->getServiceLocator()->get($widgetName);
                    $attr->addClass($data->getId());
                    $data->setAttributes($attr);
                } catch (ServiceNotFoundException $e) {
                    $data = '';
                }

                break;
            case self::LAYOUT_USER_DEFINED:
                /** @var $componentModel \PageBuilder\Model\ComponentModel */
                $componentModel = $this->getServiceLocator()->get('pagebuilder\model\component');

                /** @var $component \PageBuilder\Entity\Component */
                if ($component = $componentModel->findOneTranslatedBy(['id' => $itemId])) {
                    $data     = $this->transform($component->getContent());
                    $comId    = "data-id='{$itemType}-{$itemId}'";
                    $cssClass = trim("{$itemType} {$component->getCssClass()}");
                    $attr->addClass($cssClass)
                        ->addClass($component->getCssId())
                        ->addAttr($comId)
                        ->addAttr("id='{$component->getCssId()}'");

                    //Apply replaces to the output
                    $replacements = $this->getOptions()->getReplacements();
                    $data         = str_replace(array_keys($replacements), array_values($replacements), $data);

                    //apply formats to the data
                    $data = $this->applyFormats($data);
                }

                break;
            default:
                $message = 'Layout itemType ' . $itemType . ' not found';
                if ($this->serviceManager->has('logger')) {
                    $this->serviceManager->get('logger')->err($message);
                } else {
                    throw new RuntimeException($message);
                }
        }

        return new WidgetData(
            [
                'data'       => $data,
                'attributes' => $attr,
            ]
        );
    }

    /**
     * Apply formatter using the formatters in the order they were defined
     *
     * @param $data
     *
     * @return string
     */
    protected function applyFormats($data)
    {
        if ($this->getOptions()->getOutputFormatters()) {

            foreach ($this->getOptions()->getOutputFormatters() as $formatter) {
                if ($formatter instanceof FormatterInterface) {
                    /** @var $formatter \PageBuilder\FormatterInterface */
                    $data = $formatter->format($data);
                } elseif (is_callable($formatter)) {
                    $data = $formatter($data, $this->serviceManager);
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
        return $this->layout;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    private function setPluginManager(ServiceLocatorInterface $serviceLocator)
    {
        $this->pluginManager = $serviceLocator->get('ViewHelperManager');

        return $this;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;
        $this->setPluginManager($serviceLocator);

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @param TagAttributes $attr
     * @param TagAttributes $attr
     * @param string $section
     *
     * @return array
     */
    protected function getTopBottomContainers(TagAttributes $attr, $section = '')
    {
        $top = $bottom = '';
        if ($wrapper = $attr->getWrapper()) {
            /** @var $microDataHelper MicroData */
            $microDataHelper = $this->pluginManager->get('microData');

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

            $variables = [
                trim($wrapper),
                $this->transform(trim($attr->formatClass())),
                trim($attr->formatId()),
                trim($attr->formatAttr()),
                trim($microData),
            ];

            $variables = array_filter($variables);

            $top .= sprintf('<%s>', implode(' ', $variables));
        }

        if ($containerClass = $attr->getContainer()) {
            $top .= '<div class="' . $this->transform($containerClass) . '">';
            $bottom .= '</div>';

            if ($container2 = $attr->getContainer2()) {
                $top .= '<div class="' . $this->transform($container2) . '">';
                $bottom .= '</div>';
            }
        }

        //main wrapper close
        if ($wrapper) {
            $bottom .= '</' . $wrapper . '>';
        }

        return [$top, $bottom];
    }

    /**
     * @param \PageBuilder\View\Helper\Config\PageBuilderConfig $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return \PageBuilder\View\Helper\Config\PageBuilderConfig
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options
     *
     * @return bool
     */
    private function isShared($options)
    {
        if (!array_key_exists(self::SHARE_KEY, $options)) {
            return true;
        }

        if (is_bool($options[self::SHARE_KEY])) {
            return $options[self::SHARE_KEY];
        }
        if (is_numeric($options[self::SHARE_KEY])) {
            $shared = $options[self::SHARE_KEY] * 1;

            return ($shared <= 0) ? false : true;
        }

        if ($options[self::SHARE_KEY] == 'true') {
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
    protected function transform($class)
    {
        if ($this->options->getBootstrapVersion() > 2) {
            foreach ($this->options->getCssClassmap() as $search => $replace) {
                $pattern = '/' . $search . '/i';
                if (preg_match($pattern, $class, $matches)) {
                    $done = str_replace($search, $replace, $class);

                    return $done;
                }
            }
        }

        return $class;
    }
}
