<?php
namespace PageBuilder\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class PageBuilder
    extends AbstractPlugin
{
    public function __invoke()
    {
        /** @var $oController \Zend\Mvc\Controller\AbstractController */
        $oController = $this->getController();
        $pageBuilder = $oController->getServiceLocator()->get('viewhelpermanager')->get('buildPage');

        return $pageBuilder;
    }
}
