<?php

namespace PageBuilder;

use PageBuilder\View\TagAttributes;
use SynergyCommon\Service\ServiceLocatorAwareInterface;
use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\AbstractHelper;

/**
 * Class BaseWidget
 * @method \Zend\View\Model\ViewModel getView()
 *
 * @package PageBuilder
 */
abstract class BaseWidget extends AbstractHelper implements WidgetInterface, ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    const CAT_GENERAL = 'General';
    protected $allowEmpty = false;
    protected $id;
    protected $name;
    protected $description;
    protected $category;
    protected $class = array();
    /**
     * @var TagAttributes
     */
    protected $attributes;
    protected $_mvcEvent;
    protected $options
        = array(
            'shared' => true
        );

    public function init()
    {
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * @param boolean $allowEmpty
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;
    }

    public function getCategory()
    {
        return $this->category ?: self::CAT_GENERAL;
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

    /**
     * @return array
     */
    protected function getMergedOptions()
    {
        if ($this->getAttributes()) {

            return array_merge($this->options, $this->getAttributes()->getOptions());
        }

        return $this->options;
    }

    protected function getHelper($helper)
    {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');

        return $viewHelperManager->get($helper);
    }
}
