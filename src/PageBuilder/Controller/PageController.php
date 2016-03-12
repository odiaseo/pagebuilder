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

    /**
     * Update page layout
     *
     * @param mixed $id
     * @param mixed $data
     *
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function update($id, $data)
    {
        $layout = isset($data['layout']) ? $data['layout'] : null;

        return $this->_sendPayload(
            $this->_getService($this->_pageServiceKey)->updatePageLayout($id, $data['themeId'], $layout)
        );
    }
}
