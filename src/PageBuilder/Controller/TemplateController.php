<?php
namespace PageBuilder\Controller;

/**
 * Class TemplateController
 *
 * @package PageBuilder\Controller
 */
class TemplateController extends BasePageRestfulController
{
    /**
     * Get template details
     *
     * @param mixed $id
     *
     * @return mixed|\Laminas\View\Model\ModelInterface
     */
    public function get($id)
    {
        return $this->_sendPayload(
            $this->_getService($this->_pageServiceKey)->getTemplateLayout($id)
        );
    }

    /**
     * Update template
     *
     * @param mixed $id
     * @param mixed $data
     *
     * @return mixed|\Laminas\View\Model\ModelInterface
     */
    public function update($id, $data)
    {
        $layout = isset($data['layout']) ? $data['layout'] : null;

        return $this->_sendPayload(
            $this->_getService($this->_pageServiceKey)->updateTemplateLayout($id, $layout)
        );
    }
}
