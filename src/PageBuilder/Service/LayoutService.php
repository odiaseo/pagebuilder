<?php
namespace PageBuilder\Service;

use PageBuilder\Entity\Join\TemplateSection;
use PageBuilder\Model\PageModel;
use PageBuilder\View\Helper\PageBuilder;
use SynergyCommon\Service\ServiceLocatorAwareInterface;
use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceManager;

/**
 * Class LayoutService
 * @package PageBuilder\Service
 */
class LayoutService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * LayoutService constructor.
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->setServiceLocator($serviceManager);
    }

    public function updateTemplateSections($templateId, $sections)
    {

        $error = false;

        /** @var $templateModel \PageBuilder\Model\TemplateModel */
        $templateModel = $this->getServiceLocator()->get('pagebuilder\model\template');

        try {
            $templateModel->updateTemplateSections($templateId, $sections);
            $message = sprintf('Template (#%d) sections updated successfully', $templateId);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error   = true;
        }

        return array(
            'message' => $message,
            'error'   => $error
        );
    }

    public function getActiveTemplateSections($templateId)
    {
        $sections = $selected = array();

        /** @var $templateModel \PageBuilder\Model\TemplateModel */
        $templateModel    = $this->getServiceLocator()->get('pagebuilder\model\template');
        $templateSections = $templateModel->getActiveSections($templateId);

        $sectionModel = $this->getServiceLocator()->get('pagebuilder\model\section');
        $sectionList  = $sectionModel->findAll();

        /** @var \PageBuilder\Entity\Join\TemplateSection $templateSection */
        foreach ($templateSections as $templateSection) {
            $id            = $templateSection->getSectionId()->getId();
            $sortOrder     = $templateSection->getSortOrder();
            $title         = $templateSection->getSectionId()->getTitle() . ' (' . $sortOrder . ')';
            $sections[$id] = array(
                'title' => $title,
                'order' => $sortOrder
            );
            $selected[]    = $id;
        }

        /** @var \PageBuilder\Entity\Section $section */
        foreach ($sectionList as $section) {
            $id = $section->getId();
            if (!isset($sections[$id])) {
                $sections[$id] = array(
                    'title' => $section->getTitle(),
                    'order' => 0
                );
            }
        }

        ksort($selected);

        return array(
            'templateSections' => $selected,
            'sections'         => (object)$sections
        );
    }

    /**
     * Get Page theme layout
     *
     * @param $pageThemeId
     *
     * @return array
     */
    public function getPageThemeLayout($pageThemeId)
    {
        /** @var $themeModel \PageBuilder\Model\PageTemplateModel */
        $themeModel = $this->getServiceLocator()->get('pagebuilder\model\pageTheme');

        $pageTheme = $themeModel->findObject($pageThemeId);

        return $this->getPageLayout($pageTheme->getPageId()->getId(), $pageTheme->getThemeId()->getId());
    }

    /**
     * Get page layout
     *
     * @param      $pageId
     * @param null $pageThemeId
     *
     * @return array
     */
    public function getPageLayout($pageId, $pageThemeId = null)
    {
        $sections = $templateSections = array();
        $error    = '';
        $details  = [];

        /** @var $pageModel \PageBuilder\Model\PageModel */
        $pageModel = $this->getServiceLocator()->get('pagebuilder\model\page');

        /** @var $themeModel \PageBuilder\Model\BaseModel */
        $themeModel = $this->getServiceLocator()->get('pagebuilder\model\theme');

        /** @var $templateModel \PageBuilder\Model\BaseModel */
        $templateModel = $this->getServiceLocator()->get('pagebuilder\model\template');

        /** @var $page \PageBuilder\Entity\Page */
        if ($page = $pageModel->getRepository()->find($pageId)) {

            $details = array(
                'id'          => $page->getId(),
                'title'       => $page->getTitle(),
                'description' => $page->getDescription(),
                'template'    => $page->getTemplate() ? $page->getTemplate()->getTitle() : '',
                'layout'      => '',
                'pageTheme'   => '',
                'themeId'     => '',
                'layoutType'  => 'Custom Layout',
                'parent'      => $page->getParent() ? $page->getParent()->getTitle() : ''

            );
        }
        $themeId = $pageTheme = null;
        if ($pageThemeId) {
            /** @var $pageThemeModel \PageBuilder\Model\BaseModel */
            $pageThemeModel = $this->getServiceLocator()->get('pagebuilder\model\pageTheme');
            $pageTheme      = $pageThemeModel->findObject($pageThemeId);
            $themeId        = $pageTheme->getId();
        }

        if ($themeId and $pageTheme) {
            $details['themeId'] = $themeId;

            $details['layout']    = $pageTheme->getLayout();
            $details['pageTheme'] = $pageTheme->getId();

            /** @var $temp \PageBuilder\Entity\Template */
            if ($temp = $page->getTemplate()) {
                $sections = $temp->getTemplateSections()->toArray();
            } elseif ($temp = $page->getParent()->getTemplate()) {
                $sections = $temp->getTemplateSections()->toArray();
            }
        }
        /** @var $section \PageBuilder\Entity\Join\TemplateSection */
        foreach ($sections as $section) {
            $slug                    = $section->getSectionId()->getSlug();
            $templateSections[$slug] = array(
                'title'  => $section->getSectionId()->getTitle(),
                'status' => $section->getIsActive() ? 1 : 0,
                'class'  => $section->getIsActive() ? '' : 'in-active'
            );
        }

        if ($themeData = $themeModel->getRepository()->findAll()) {
            /** @var $theme \PageBuilder\Entity\Theme */
            foreach ($themeData as $theme) {
                $details['themes'][$theme->getId()] = $theme->toArray();
            }
        } else {
            $details['themes'] = array();
        }

        /** @var $templateModel \PageBuilder\Model\TemplateModel */
        $templates  = $templateModel->listTemplates();
        $components = $this->getServiceLocator()->get('pagebuilder\model\component')->listItemsByTitle();

        /** @var $widgetUtil \PageBuilder\Util\Widget */
        $widgetUtil = $this->getServiceLocator()->get('util\widget');

        $widgetList = $widgetUtil->getWidgetList();
        $urlHelper  = $this->getServiceLocator()->get('ViewHelperManager')->get('url');

        $return = array(
            'error'     => $error,
            'page'      => $details,
            'editUrl'   => $pageThemeId
                ? $urlHelper('builder\theme', array('id' => $pageThemeId))
                : $urlHelper(
                    'builder', array('id' => $pageId)
                ),
            'sections'  => $templateSections,
            'title'     => 'Layout Manager - ' . ($page ? $page->getTitle() : ''),
            'widgets'   => array(
                'title' => 'Widgets',
                'items' => $widgetUtil->getRegistry(),
                'total' => count($widgetUtil->getRegistry()),
                'list'  => $widgetList,
                'id'    => PageBuilder::LAYOUT_WIDGET
            ),
            'assets'    => array(
                PageBuilder::LAYOUT_USER_DEFINED => array(
                    'title' => 'User Defined',
                    'items' => $components
                )
            ),
            'templates' => array(
                'title' => 'Templates',
                'items' => $templates
            ),
            'tags'      => $this->_getTaglist()
        );

        return $return;
    }

    public function getTemplateLayout($templateId)
    {
        $templateSections = array();
        $error            = '';
        $sections         = [];
        $details          = [];

        /** @var $themeModel \PageBuilder\Model\BaseModel */
        $themeModel = $this->getServiceLocator()->get('pagebuilder\model\theme');

        /** @var $templateModel \PageBuilder\Model\TemplateModel */
        $templateModel = $this->getServiceLocator()->get('pagebuilder\model\template');

        /** @var $template \PageBuilder\Entity\Template */
        /** @var TemplateSection $templateSection */

        if ($template = $templateModel->getRepository()->find($templateId)) {
            foreach ($template->getTemplateSections() as $templateSection) {
                //$sections[$templateSection->getSortOrder()] = $templateSection;
                $key            = $templateSection->getSortOrder() . '-' . $templateSection->getId();
                $sections[$key] = $templateSection;
            }

            ksort($sections);

            $details          = $template->toArray();
            $details['theme'] = array(
                'id'        => null,
                'title'     => '',
                'pageTheme' => ''
            );
        }

        /** @var $section \PageBuilder\Entity\Join\TemplateSection */
        foreach ($sections as $section) {
            $slug                    = $section->getSectionId()->getSlug();
            $templateSections[$slug] = array(
                'title'  => $section->getSectionId()->getTitle(),
                'class'  => $section->getIsActive() ? '' : 'in-active',
                'status' => $section->getIsActive() ? 1 : 0
            );
        }

        if ($themeData = $themeModel->getRepository()->findAll()) {
            /** @var $theme \PageBuilder\Entity\Theme */
            foreach ($themeData as $theme) {
                $details['themes'][$theme->getId()] = $theme->toArray();
            }
        } else {
            $details['themes'] = array();
        }

        $templates  = $templateModel->listTemplates();
        $components = $this->getServiceLocator()->get('pagebuilder\model\component')->listItemsByTitle();

        /** @var $widgetUtil \PageBuilder\Util\Widget */
        $widgetUtil = $this->getServiceLocator()->get('util\widget');
        $widgetList = $widgetUtil->getWidgetList();
        $urlHelper  = $this->getServiceLocator()->get('ViewHelperManager')->get('url');

        $return = array(
            'error'     => $error,
            'page'      => $details,
            'editUrl'   => $urlHelper('template', array('id' => $templateId)),
            'sections'  => $templateSections,
            'title'     => 'Layout Manager - ' . ($template ? $template->getTitle() : ''),
            'widgets'   => array(
                'title' => 'Widgets',
                'items' => $widgetUtil->getRegistry(),
                'total' => count($widgetUtil->getRegistry()),
                'list'  => $widgetList,
                'id'    => PageBuilder::LAYOUT_WIDGET
            ),
            'assets'    => array(
                PageBuilder::LAYOUT_USER_DEFINED => array(
                    'title' => 'User Defined',
                    'items' => $components
                )
            ),
            'templates' => array(
                'title' => 'Templates',
                'items' => $templates
            ),
            'tags'      => $this->_getTaglist()
        );

        return $return;
    }

    public function updateTemplateLayout($templateId, $layout)
    {
        try {

            /** @var $templateModel \PageBuilder\Model\TemplateModel */
            $templateModel = $this->getServiceLocator()->get('pagebuilder\model\template');

            /** @var \PageBuilder\Entity\Template $template */
            if ($template = $templateModel->findObject($templateId)) {
                $template->setlayout($layout);
                $templateModel->save($template);
            }

            return array(
                'error'   => false,
                'message' => sprintf('Template #%d updated successfully', $templateId)
            );
        } catch (\Exception $exception) {
            return array(
                'error'   => true,
                'message' => $exception->getMessage()
            );
        }
    }

    protected function _getTaglist()
    {
        $tagList = array();
        /** @var $builder \PageBuilder\View\Helper\PageBuilder */
        $builder = $this->getServiceLocator()->get('ViewHelperManager')->get('buildPage');

        foreach ($builder->getOptions()->getTags() as $type => $list) {
            asort($list);
            $tagList[$type] = $list;
        }

        return $tagList;
    }

    /**
     * @param      $pageId
     * @param null $themeId
     *
     * @return array
     */
    public function resolvePageLayout($pageId, $themeId = null)
    {
        /** @var $site \PageBuilder\Entity\Site */
        /** @var $parent \PageBuilder\Entity\Page */
        /** @var $page \PageBuilder\Entity\Page */
        /** @var $templateObj \PageBuilder\Entity\Template */
        /** @var PageModel $pageModel */

        $layout    = array();
        $pageModel = $this->getServiceLocator()->get('pagebuilder\model\page');
        $page      = $pageModel->getMainPageById($pageId);

        if ($page['layout']) {
            return $page['layout'];
        } elseif ($page['parentId']) {
            $parentPage = $pageModel->getMainPageById($page['parentId']);
            if ($parentPage['layout']) {
                return $parentPage['layout'];
            }
        }

        if (is_object($page) and $templateObj = $page->getTemplate()) {
            if ($layout = $templateObj->getLayout()) {
                return $layout;
            }
        }

        //get the sites default template's layout
        if ($site = $this->getServiceLocator()->get('active\site')) {
            if ($templateObj = $site->getDefaultTemplate()) {
                return $templateObj->getLayout();
            }
        }

        return $layout;
    }
}
