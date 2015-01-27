<?php
namespace PageBuilder;

use Zend\Stdlib\AbstractOptions;

class WidgetData
    extends AbstractOptions
{
    /** @var \PageBuilder\View\TagAttributes */
    public $attributes;

    /** @var string | \PageBuilder\BaseWidget */
    public $data;

    /**
     * @param \PageBuilder\View\TagAttributes $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return \PageBuilder\View\TagAttributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param \PageBuilder\BaseWidget|string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return \PageBuilder\BaseWidget|string
     */
    public function getData()
    {
        return $this->data;
    }

}
