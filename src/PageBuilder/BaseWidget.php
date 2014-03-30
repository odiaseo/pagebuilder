<?php

namespace PageBuilder;


use PageBuilder\View\TagAttributes;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\View\Helper\AbstractHelper;

abstract class BaseWidget extends AbstractHelper implements
    ServiceManagerAwareInterface,
    WidgetInterface

{
    const CAT_GENERAL = 'General';
    protected $id;
    protected $name;
    protected $description;
    protected $category;
    protected $class = array();
    /** @var \PageBuilder\View\TagAttributes */
    protected $attributes;
    protected $_mvcEvent;
    protected $options
        = array(
            'shared' => true
        );
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $_serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->_serviceManager = $serviceManager;

        return $this;

    }

    public function init()
    {
        return $this;
    }

    public function getCategory()
    {
        return $this->category ? : self::CAT_GENERAL;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getName()
    {
        if (!$this->name) {
            $this->name = get_class($this);
        }

        return $this->name;
    }

    public function setMvcEvent(MvcEvent $e)
    {
        $this->_mvcEvent = $e;

        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setOptions($options)
    {
        $this->options = $options;

    }

    public function mergeOptions(array $options)
    {
        $this->options = array_merge_recursive($this->options, $options);

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function setAttributes(TagAttributes $attr)
    {
        $this->attributes = $attr;

        return $this;
    }

    protected function getMergedOptions()
    {
        return array_merge($this->options, $this->getAttributes()->getOptions());
    }

    protected function getHelper($helper)
    {
        $viewHelperManager = $this->_serviceManager->get('viewHelperManager');

        return $viewHelperManager->get($helper);
    }

}