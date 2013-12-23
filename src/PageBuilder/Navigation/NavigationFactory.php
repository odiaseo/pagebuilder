<?php
namespace PageBuilder\Navigation;

use Zend\Navigation\Navigation;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Navigation\Exception;

class NavigationFactory extends DefaultNavigationFactory
{
    protected $_sm;
    protected $_service = 'pagebuilder\model\page';


    protected function getName()
    {
        return 'menu';
    }

    protected function getPagesFromConfig($config = null)
    {
        if (is_callable($config)) {
            return $config($this->_sm);
        }
    }

    protected function getPages($serviceManager)
    {
        /** @var $navService \PageBuilder\Service\PageService */
        $navService = $serviceManager->get($this->_service);
        /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repo  = $navService->getRepository(); //  $em->getRepository($navService->getEntity());
        $menus = $repo->getNodesHierarchy();

        $pages       = $navService->toHierarchy($menus);
        $this->pages = $this->preparePages($serviceManager, $pages);

        return $this->pages;
    }

    public function getNavigationByRootId($id)
    {
        /** @var $navService \PageBuilder\Service\PageService */
        $navService = $this->_sm->get($this->_service);

        $repo     = $navService->getRepository()->getNodesHierarchy();
        $rootMenu = $navService->getRootMenuById($id);
        $menus    = $repo->getNodesHierarchy($rootMenu);

        $pages = $navService->toHierarchy($menus);
        $pages = $this->preparePages($this->_sm, $pages);

        return new Navigation($pages);
    }
}