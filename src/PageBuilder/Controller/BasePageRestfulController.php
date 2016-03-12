<?php
namespace PageBuilder\Controller;

use SynergyCommon\Controller\BaseRestfulController;

/**
 * Class BaseRestfulController
 * @package PageBuilder\Controller
 */
class BasePageRestfulController extends BaseRestfulController
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
