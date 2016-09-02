<?php
namespace PageBuilder\Controller\Plugin;

use SynergyCommon\Controller\BaseActionController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class PageBuilder
 *
 * @package PageBuilder\Controller\Plugin
 */
class PageBuilder extends AbstractPlugin
{
    public function __invoke()
    {
        /** @var $oController BaseActionController */
        $oController = $this->getController();
        $pageBuilder = $oController->getServiceLocator()->get('ViewHelperManager')->get('buildPage');

        return $pageBuilder;
    }
}
