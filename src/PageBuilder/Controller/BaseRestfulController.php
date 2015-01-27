<?php
namespace PageBuilder\Controller;

use SynergyCommon\Controller\BaseRestfulController as BaseRestController;

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
