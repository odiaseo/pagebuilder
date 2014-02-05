<?php
namespace PageBuilder\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

class TemplateSectionController extends AbstractRestfulController
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
        /** @var \PageBuilder\Service\LayoutService $service */
        $service = $this->getServiceLocator()->get('pagebuilder\service\layout');

        $payLoad   = $service->getActiveTemplateSections($id);
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);
        $viewModel->setVariables($payLoad);

        return $viewModel;
    }

    public function update($id, $data)
    {
        /** @var \PageBuilder\Service\LayoutService $service */
        $service = $this->getServiceLocator()->get('pagebuilder\service\layout');

        $payLoad   = $service->updateTemplateSections($id, $data['sections']);
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);
        $viewModel->setVariables($payLoad);

        return $viewModel;
    }
}