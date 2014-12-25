<?php
namespace PageBuilder\Navigation;

use Zend\Navigation\Exception;
use Zend\Navigation\Navigation;
use Zend\Navigation\Service\DefaultNavigationFactory;

class NavigationFactory extends DefaultNavigationFactory {
	protected $_sm;
	protected $_model = 'pagebuilder\model\page';


	protected function getName() {
		return 'pagebuilder\menu';
	}

	protected function getPagesFromConfig( $config = null ) {
		if ( is_callable( $config ) ) {
			return $config( $this->_sm );
		}
	}

	protected function getPages( $serviceManager ) {
		/** @var $pageModel \PageBuilder\Model\PageModel */
		$pageModel = $serviceManager->get( $this->_model );

		/** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
		$repo  = $pageModel->getRepository(); //  $em->getRepository($navService->getEntity());
		$menus = $repo->getNodesHierarchy();

		$pages       = $pageModel->toHierarchy( $menus );
		$this->pages = $this->preparePages( $serviceManager, $pages );

		return $this->pages;
	}

	public function getNavigationByRootId( $id ) {
		/** @var $pageModel \PageBuilder\Model\PageModel */
		$pageModel = $this->_sm->get( $this->_model );

		/** @var $repo \Gedmo\Tree\Entity\Repository\NestedTreeRepository */
		$repo     = $pageModel->getRepository();
		$rootMenu = $pageModel->getRootMenuById( $id );
		$menus    = $repo->getNodesHierarchy( $rootMenu );

		$pages = $pageModel->toHierarchy( $menus );
		$pages = $this->preparePages( $this->_sm, $pages );

		return new Navigation( $pages );
	}
}
