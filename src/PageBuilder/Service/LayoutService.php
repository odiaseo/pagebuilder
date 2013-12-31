<?php
namespace PageBuilder\Service;

use PageBuilder\Entity\Join\PageTheme;
use PageBuilder\View\Helper\PageBuilder;
use PageBuilder\WidgetFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class LayoutService implements ServiceManagerAwareInterface
{
    /** @var  \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_serviceManager = $serviceManager;
    }

    public function updateTemplateSections($templateId, $sections)
    {

        $error = false;

        /** @var $templateModel \PageBuilder\Model\TemplateModel */
        $templateModel = $this->_serviceManager->get('pagebuilder\model\template');

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
        $templateModel    = $this->_serviceManager->get('pagebuilder\model\template');
        $templateSections = $templateModel->getActiveSections($templateId);

        $sectionModel = $this->_serviceManager->get('pagebuilder\model\section');
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

        return array(
            'templateSections' => $selected,
            'sections'         => (object)$sections
        );
    }

    public function getPageLayout($pageId)
    {
        $sections = $templateSections = array();
        $error    = '';

        /** @var $pageModel \PageBuilder\Model\PageModel */
        $pageModel = $this->_serviceManager->get('pagebuilder\model\page');

        /** @var $themeModel \PageBuilder\Model\BaseModel */
        $themeModel = $this->_serviceManager->get('pagebuilder\model\theme');


        /** @var $templateModel \PageBuilder\Model\BaseModel */
        $templateModel = $this->_serviceManager->get('pagebuilder\model\template');


        /** @var $page \PageBuilder\Entity\Page */
        $page = $pageModel->getRepository()->find($pageId);

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

        /** @var $pageTheme \PageBuilder\Entity\Join\PageTheme */
        foreach ($page->getPageThemes() as $pageTheme) {

            if ($pageTheme->getIsActive()) {
                $themeId            = $pageTheme->getThemeId()->getId();
                $details['themeId'] = $themeId;

                $details['layout']    = $pageTheme->getLayout();
                $details['pageTheme'] = $pageTheme->getId();

                /** @var $temp \PageBuilder\Entity\Template */
                if ($temp = $page->getTemplate()) {
                    $sections = $temp->getTemplateSections()->toArray();
                } elseif ($temp = $page->getParent()->getTemplate()) {
                    $sections = $temp->getTemplateSections()->toArray();
                }

                break;
            }
        }

        /** @var $section \PageBuilder\Entity\Join\TemplateSection */
        foreach ($sections as $section) {
            $slug                    = $section->getSectionId()->getSlug();
            $templateSections[$slug] = array(
                'title' => $section->getSectionId()->getTitle(),
                'class' => $section->getIsActive() ? '' : 'in-active'
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

        $templates  = $templateModel->listItemsByTitle();
        $components = $this->_serviceManager->get('pagebuilder\model\component')->listItemsByTitle();

        /** @var $widgetUtil \PageBuilder\Util\Widget */
        $widgetUtil = $this->_serviceManager->get('util\widget');

        $widgetList = $widgetUtil->getWidgetList();
        $urlHelper  = $this->_serviceManager->get('viewhelpermanager')->get('url');


        $return = array(
            'error'     => $error,
            'page'      => $details,
            'editUrl'   => $urlHelper('builder', array('id' => $pageId)),
            'sections'  => $templateSections,
            'title'     => 'Layout Manager - ' . $page->getTitle(),
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

    public function updatePageLayout($pageId, $themeId, $layout)
    {
        try {
            /** @var $service \PageBuilder\Model\PageModel */
            $pageThemeModel = $this->_serviceManager->get('pagebuilder\model\pageTheme');

            /** @var \PageBuilder\Entity\Join\PageTheme $pageTheme */
            if (!$pageTheme = $pageThemeModel->find($themeId)) {
                $pageTheme = new PageTheme();
                $pageTheme->setPageId($pageId);
                $pageTheme->setThemeId($themeId);
            }
            $pageTheme->setLayout($layout);

            $pageThemeModel->save($pageTheme);

            return array(
                'error'   => false,
                'message' => sprintf('Page #%d updated successfully', $pageId)
            );
        } catch (\Exception $exception) {
            return array(
                'error'   => true,
                'message' => $exception->getMessage()
            );
        }
    }

    public function getTemplateLayout($templateId)
    {
        $templateSections = array();
        $error            = '';

        /** @var $themeModel \PageBuilder\Model\BaseModel */
        $themeModel = $this->_serviceManager->get('pagebuilder\model\theme');


        /** @var $templateModel \PageBuilder\Model\BaseModel */
        $templateModel = $this->_serviceManager->get('pagebuilder\model\template');


        /** @var $template \PageBuilder\Entity\Template */
        $template = $templateModel->getRepository()->find($templateId);
        $sections = $template->getTemplateSections()->toArray();

        $details          = $template->toArray();
        $details['theme'] = array(
            'id'        => null,
            'title'     => '',
            'pageTheme' => ''
        );

        /** @var $section \PageBuilder\Entity\Join\TemplateSection */
        foreach ($sections as $section) {
            $slug                    = $section->getSectionId()->getSlug();
            $templateSections[$slug] = array(
                'title' => $section->getSectionId()->getTitle(),
                'class' => $section->getIsActive() ? '' : 'in-active'
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

        $templates  = $templateModel->listItemsByTitle();
        $components = $this->_serviceManager->get('pagebuilder\model\component')->listItemsByTitle();

        /** @var $widgetUtil \PageBuilder\Util\Widget */
        $widgetUtil = $this->_serviceManager->get('util\widget');
        $widgetList = $widgetUtil->getWidgetList();
        $urlHelper  = $this->_serviceManager->get('viewhelpermanager')->get('url');

        $return = array(
            'error'     => $error,
            'page'      => $details,
            'editUrl'   => $urlHelper('template', array('id' => $templateId)),
            'sections'  => $templateSections,
            'title'     => 'Layout Manager - ' . $template->getTitle(),
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

        );

        return $return;
    }

    public function updateTemplateLayout($templateId, $layout)
    {
        try {

            /** @var $templateModel \PageBuilder\Model\TemplateModel */
            $templateModel = $this->_serviceManager->get('pagebuilder\model\template');

            /** @var \PageBuilder\Entity\Template $template */
            $template = $templateModel->findObject($templateId);
            $template->setlayout($layout);
            $templateModel->save($template);

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
        $config  = $this->_serviceManager->get('config');

        foreach ($config['pagebuilder']['tags'] as $type => $list) {
            $tagList[$type] = asort($list);
        }

        return $tagList;
    }
}