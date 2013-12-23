<?php
namespace PageBuilder\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

class TemplateController extends AbstractRestfulController
{

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

    public function get($id)
    {
        /** @var \PageBuilder\Service\TemplateService $service */
        $service = $this->getServiceLocator()->get('pagebuilder\service\template');

        $payLoad   = $service->getPageLayout($id);
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);
        $viewModel->setVariables($payLoad);

        return $viewModel;
    }

    public function getList()
    {
        /** @var \PageBuilder\Service\LayoutService $service */
        $service = $this->getServiceLocator()->get('pagebuilder\service\layout');

        $payLoad   = array('test' => time());
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);
        $viewModel->setVariables($payLoad);

        return $viewModel;
    }

    public function update($id, $data)
    {


    }

    public function create($data)
    {

    }
}