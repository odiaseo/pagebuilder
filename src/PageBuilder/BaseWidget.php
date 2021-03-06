<?php

namespace PageBuilder;

use Interop\Container\ContainerInterface;
use PageBuilder\View\TagAttributes;
use SynergyCommon\Service\ServiceLocatorAwareInterface;
use SynergyCommon\Service\ServiceLocatorAwareTrait;
use Laminas\EventManager\EventInterface;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\Helper\Escaper\AbstractHelper;
use Laminas\View\Helper\HelperInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Renderer\RendererInterface as Renderer;

/**
 * Class BaseWidget.
 */
abstract class BaseWidget implements WidgetInterface, HelperInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const CAT_GENERAL = 'General';

    /**
     * @var bool
     */
    private $initialised = false;

    /** @var PhpRenderer */
    protected $view;

    /**
     * @var bool
     */
    protected $allowEmpty = false;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var array
     */
    protected $class = [];

    /**
     * @var TagAttributes
     */
    protected $attributes;

    /**
     * @var EventInterface
     */
    protected $mvcEvent;

    /**
     * @var array
     */
    protected $options
        = [
            'shared' => true,
        ];

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * BaseWidget constructor.
     *
     * @param Renderer $view
     * @param ContainerInterface $serviceLocator
     * @param EventInterface|null $event
     */
    public function __construct(Renderer $view, ContainerInterface $serviceLocator = null, EventInterface $event = null)
    {
        if ($event) {
            $this->setMvcEvent($event);
        }

        if ($serviceLocator) {
            $this->setServiceLocator($serviceLocator);
        }

        $this->setView($view);
    }

    public function init()
    {
        $this->initialised = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInitialised()
    {
        return $this->initialised;
    }

    /**
     * @return bool
     */
    public function isAllowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * @param bool $allowEmpty
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category ?: self::CAT_GENERAL;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->name) {
            $this->name = get_class($this);
        }

        return $this->name;
    }

    /**
     * @param EventInterface $e
     *
     * @return $this
     */
    public function setMvcEvent(EventInterface $e)
    {
        $this->mvcEvent = $e;

        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return TagAttributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return array
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function mergeOptions(array $options)
    {
        $this->options = array_merge_recursive($this->options, $options);

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param TagAttributes $attr
     *
     * @return $this
     */
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

    /**
     * Set the View object.
     *
     * @param Renderer $view
     *
     * @return HelperInterface
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
    }

    /**
     * Get the View object.
     *
     * @return PhpRenderer
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param $name
     *
     * @return AbstractHelper
     */
    public function getHelper($name)
    {
        return $this->getView()->getHelperPluginManager()->get($name);
    }
}
