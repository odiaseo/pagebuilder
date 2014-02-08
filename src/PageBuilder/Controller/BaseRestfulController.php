<?php
namespace PageBuilder\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

class BaseRestfulController
    extends AbstractRestfulController
{

    protected $_pageServiceKey = 'pagebuilder\service\layout';
    /**
     * Accept header criteria
     *
     * @var array
     */
    protected $_acceptCriteria
        = array(
            'Zend\View\Model\JsonModel' => array(
                'application/json',
                'application/jsonp',
                'application/javascript',
                '*/*'
            ),
            'Zend\View\Model\ViewModel' => array(
                '*/*'
            ),
        );

    protected function _sendPayload($payLoad)
    {
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);
        $viewModel->setVariables($payLoad);

        return $viewModel;
    }

    /**
     * @param $key
     *
     * @return   \PageBuilder\Service\LayoutService $service
     */
    protected function _getService($key)
    {
        return $this->getServiceLocator()->get($key);
    }
}