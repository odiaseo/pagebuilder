<?php
namespace PageBuilder\Controller;

use SynergyCommon\Controller\BaseRestfulController as BaseRestController;
use Zend\Mvc\Controller\AbstractRestfulController;

class BaseRestfulController
    extends BaseRestController
{
    protected $_pageServiceKey = 'pagebuilder\service\layout';

    /**
     * @param $key
     *
     * @return   \PageBuilder\Service\LayoutService $service
     */
    protected function _getService($key = null)
    {
        return $this->getServiceLocator()->get($key);
    }
}