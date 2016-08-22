<?php
namespace PageBuilder\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class PageBuilder
 * @package PageBuilder\Controller\Plugin
 */
class PageBuilder extends AbstractPlugin
{
    public function __invoke()
    {
        /** @var $oController \Zend\Mvc\Controller\AbstractController */
        $oController = $this->getController();
        $pageBuilder = $oController->getServiceLocator()->get('ViewHelperManager')->get('buildPage');

        return $pageBuilder;
    }
}
