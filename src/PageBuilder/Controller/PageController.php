<?php
namespace PageBuilder\Controller;

/**
 * Class PageController
 *
 * @package PageBuilder\Controller
 */
class PageController extends BasePageRestfulController
{

    /**
     * Get page layout details
     *
     * @param mixed $id
     *
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function get($id)
    {
        return $this->_sendPayload(
            $this->_getService($this->_pageServiceKey)->getPageLayout($id)
        );
    }
}
