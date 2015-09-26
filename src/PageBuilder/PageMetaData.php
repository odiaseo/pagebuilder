<?php
namespace PageBuilder;

use Zend\Stdlib\AbstractOptions;

/**
 * Display page header information about offer rows
 * Class PageSummary
 *
 * @package Application\DataModel
 */
class PageMetaData extends AbstractOptions
{
    public $title = '';
    public $description = '';
    public $keywords = '';

    public $image = '';
    public $alt = '';
    public $thumbnail;

    public $section = 'vouchers-discounts';
    public $categories = array();
    public $searchTerm;
    public $type = 'website';

    //SEO Header Metadata
    protected $metaDescription;
    protected $metaTitle;
    protected $metaKeywords;

    /** @var string */
    protected $domain;

    /** @var \Zend\Stdlib\AbstractOptions */
    protected $settings;
    /**
     * Used for google search input on search results
     *
     * @var string
     */
    protected $searchTermString = 'keyword';

    protected $searchResultRouteName = 'search/result';

    /**
     * @return string
     */
    public function getSearchTermString()
    {
        return $this->searchTermString;
    }

    /**
     * @param string $searchTermString
     */
    public function setSearchTermString($searchTermString)
    {
        $this->searchTermString = $searchTermString;
    }

    /**
     * @return string
     */
    public function getSearchResultRouteName()
    {
        return $this->searchResultRouteName;
    }

    /**
     * @param string $searchResultRouteName
     */
    public function setSearchResultRouteName($searchResultRouteName)
    {
        $this->searchResultRouteName = $searchResultRouteName;
    }

    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param \Zend\Stdlib\AbstractOptions $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return \Zend\Stdlib\AbstractOptions
     */
    public function getSettings()
    {
        return $this->settings;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetaDescription()
    {
        if (!$this->metaDescription) {
            $this->metaDescription = $this->description;
        }

        return $this->metaDescription;
    }

    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    public function getMetaKeywords()
    {
        if (!$this->metaKeywords) {
            $this->metaKeywords = $this->keywords;
        }

        return $this->metaKeywords;
    }

    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaTitle()
    {
        if (!$this->metaTitle) {
            $this->metaTitle = $this->title;
        }

        return $this->metaTitle;
    }

    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }
}
