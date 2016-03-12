<?php
namespace PageBuilder\Controller;

/**
 * Class TemplateSectionController
 *
 * @package PageBuilder\Controller
 */
class TemplateSectionController extends BasePageRestfulController
{
    /**
     * Get template sections
     *
     * @param mixed $id
     *
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function get($id)
    {
        return $this->_sendPayload(
            $this->_getService($this->_pageServiceKey)->getActiveTemplateSections($id)
        );
    }

    /**
     * Update section details
     *
     * @param mixed $id
     * @param mixed $data
     *
     * @return mixed|\Zend\View\Model\ModelInterface
     */
    public function update($id, $data)
    {
        return $this->_sendPayload(
            $this->_getService($this->_pageServiceKey)->updateTemplateSections($id, $data['sections'])
        );
    }
}
