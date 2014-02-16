<?php
namespace PageBuilder\View\Helper\Config;

use Zend\Stdlib\AbstractOptions;

class PageBuilderConfig
    extends AbstractOptions
{
    /**
     * Enable/Disable the view helper
     *
     * @var boolean
     */
    protected $enabled;
    /**
     * Directory locations where to find widgets
     *
     * @var array
     */
    protected $widgets = array();
    /**
     * Zend navigation alias to get the main Zend Navigation menu that drives the
     * page builder
     *
     * @var string
     */
    protected $mainNavigation;
    /**
     * @deprecated
     * @var array
     */
    protected $entities;
    /**
     * Allowed HTML tags to be used in building the page
     *
     * @var array
     *
     */
    protected $tags;
    /**
     * Map Css class to Twitter Bootstrap 2 css classes
     *
     * @var array
     */
    protected $cssClassmap;
    /**
     * Replaces placeholders
     *
     * @var array
     */
    protected $replacements;
    /**
     * Formatters to formats user defined data before rendering to the view
     *
     * @var array
     */
    protected $outputFormatters = array();

    /**
     * @param array $cssClassmap
     */
    public function setCssClassmap($cssClassmap)
    {
        $this->cssClassmap = $cssClassmap;
    }

    /**
     * @return array
     */
    public function getCssClassmap()
    {
        return $this->cssClassmap;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param array $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param string $mainNavigation
     */
    public function setMainNavigation($mainNavigation)
    {
        $this->mainNavigation = $mainNavigation;
    }

    /**
     * @return string
     */
    public function getMainNavigation()
    {
        return $this->mainNavigation;
    }

    /**
     * @param array $outputFormatters
     */
    public function setOutputFormatters($outputFormatters)
    {
        $this->outputFormatters = $outputFormatters;
    }

    /**
     * @return array
     */
    public function getOutputFormatters()
    {
        return $this->outputFormatters;
    }

    /**
     * @param array $replacements
     */
    public function setReplacements($replacements)
    {
        $this->replacements = $replacements;
    }

    /**
     * @return array
     */
    public function getReplacements()
    {
        return $this->replacements;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $widgets
     */
    public function setWidgets($widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return $this->widgets;
    }


}