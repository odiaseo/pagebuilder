<?php
namespace PageBuilder\Controller;

class PageThemeController
    extends BaseRestfulController
{

    /**
     * Get page  theme layout details
     *
     * @param mixed $id
     *
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function get($id)
    {
        return $this->_sendPayload(
            $this->_getService($this->_pageServiceKey)->getPageThemeLayout($id)
        );
    }

    /**
     * Update page theme layout
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
            $this->_getService($this->_pageServiceKey)->updatePageThemeLayout($id, $layout)
        );

    }
}
