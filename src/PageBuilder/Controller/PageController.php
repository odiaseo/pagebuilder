<?php
namespace PageBuilder\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

class PageController extends AbstractRestfulController
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

        $payLoad   = $service->getPageLayout($id);
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);
        $viewModel->setVariables($payLoad);

        return $viewModel;
    }

    public function update($id, $data)
    {
        /** @var \PageBuilder\Service\LayoutService $service */
        $service = $this->getServiceLocator()->get('pagebuilder\service\layout');
        $layout  = isset($data['layout']) ? $data['layout'] : null;

        $payLoad   = $service->updatePageLayout($id, $data['themeId'], $layout);
        $viewModel = $this->acceptableViewModelSelector($this->_acceptCriteria);
        $viewModel->setVariables($payLoad);

        return $viewModel;

    }

}