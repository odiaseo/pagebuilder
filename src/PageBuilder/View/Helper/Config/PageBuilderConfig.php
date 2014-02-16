<?php
namespace PageBuilder\View\Helper\Config;

use Zend\Stdlib\AbstractOptions;

class PageBuilderConfig
    extends AbstractOptions
{
    protected $enabled;
    protected $widgets;
    protected $mainNavigation;
    protected $entities;
    protected $tags;
    protected $cssClassmap;
    protected $replacements;

    public function setCssClassmap($cssClassmap)
    {
        $this->cssClassmap = $cssClassmap;
    }

    public function getCssClassmap()
    {
        return $this->cssClassmap;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function setMainNavigation($mainNavigation)
    {
        $this->mainNavigation = $mainNavigation;
    }

    public function getMainNavigation()
    {
        return $this->mainNavigation;
    }

    public function setReplacements($replacements)
    {
        $this->replacements = $replacements;
    }

    public function getReplacements()
    {
        return $this->replacements;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setWidgets($widgets)
    {
        $this->widgets = $widgets;
    }

    public function getWidgets()
    {
        return $this->widgets;
    }

}
