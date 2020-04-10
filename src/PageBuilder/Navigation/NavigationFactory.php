<?php
namespace PageBuilder\Navigation;

use Interop\Container\ContainerInterface;
use Laminas\Navigation\Exception;
use Laminas\Navigation\Navigation;
use Laminas\Navigation\Service\DefaultNavigationFactory;

/**
 * Class NavigationFactory
 *
 * @package PageBuilder\Navigation
 */
class NavigationFactory extends DefaultNavigationFactory
{
    protected $_sm;

    protected $_model = 'pagebuilder\model\page';

    protected function getName()
    {
        return 'pagebuilder\menu';
    }

    protected function getPagesFromConfig($config = null)
    {
        if (is_callable($config)) {
            return $config($this->_sm);
        }

        return [];
    }

    protected function getPages(ContainerInterface $serviceManager)
    {
        /** @var $pageModel \PageBuilder\Model\PageModel */
        $pageModel = $serviceManager->get($this->_model);

        /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repo  = $pageModel->getRepository(); //  $em->getRepository($navService->getEntity());
        $menus = $repo->getNodesHierarchy();

        $pages       = $pageModel->toHierarchy($menus);
        $this->pages = $this->preparePages($serviceManager, $pages);

        return $this->pages;
    }

    public function getNavigationByRootId($id)
    {
        /** @var $pageModel \PageBuilder\Model\PageModel */
        $pageModel = $this->_sm->get($this->_model);

        /** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
        $repo     = $pageModel->getRepository();
        $rootMenu = $pageModel->getRootMenuById($id);
        $menus    = $repo->getNodesHierarchy($rootMenu);

        $pages = $pageModel->toHierarchy($menus);
        $pages = $this->preparePages($this->_sm, $pages);

        return new Navigation($pages);
    }
}
