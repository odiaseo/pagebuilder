<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseSite;

/**
 * A Site.
 *
 * @ORM\Entity
 * @ORM\Table(name="Site")
 */
class Site
    extends BaseSite
{
    /**
     * @ORM\Column(type="string", length=75, nullable=true, name="display_title")
     */
    private $displayTitle;
    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $description;
    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $strapline;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\SiteTheme", mappedBy="siteId", cascade="persist")
     */
    private $siteThemes;
    /**
     * @ORM\ManyToMany(targetEntity="PageBuilder\Entity\Licence", cascade="persist")
     * @ORM\JoinTable(name="Site_Licence")
     */
    private $licences;
    /**
     * @ORM\ManyToMany(targetEntity="PageBuilder\Entity\Setting", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinTable(name="Site_Setting")
     */
    private $settings;
    /**
     * @ORM\ManyToMany(targetEntity="Module", cascade={"persist"})
     * @ORM\JoinTable(name="Site_Module")
     */
    private $modules;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Site", mappedBy="parent", cascade="persist")
     */
    private $subDomains;
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", inversedBy="subDomains")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    /**
     * @ORM\OneToOne(targetEntity="PageBuilder\Entity\Page",  cascade="persist")
     * @ORM\Column(type="integer", name="root_id", nullable=false)
     */
    private $page;
    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $defaultTimezone = 'Europe/London';

    public function __construct()
    {
        $this->licences   = new ArrayCollection();
        $this->settings   = new ArrayCollection();
        $this->siteThemes = new ArrayCollection();
        $this->modules    = new ArrayCollection();
        $this->subDomains = new ArrayCollection();

    }

    public function setPage($rootPage)
    {
        $this->page = $rootPage;
    }

    public function getPage()
    {
        return $this->page;
    }


    public function setDisplayTitle($displayTitle)
    {
        $this->displayTitle = $displayTitle;
    }

    public function getDisplayTitle()
    {
        return $this->displayTitle;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Site
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setSubDomains($subDomains)
    {
        $this->subDomains = $subDomains;
    }

    public function getSubDomains()
    {
        return $this->subDomains;
    }

    public function setDefaultTimezone($timezone)
    {
        $this->defaultTimezone = $timezone;
    }

    public function getDefaultTimezone()
    {
        return $this->defaultTimezone;
    }


    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    public function getModules()
    {
        return $this->modules;
    }


    public function setSiteThemes($siteThemes)
    {
        $this->siteThemes = $siteThemes;
    }

    public function getSiteThemes()
    {
        return $this->siteThemes;
    }

    public function setLicences($licences)
    {
        $this->licences = $licences;
    }

    public function getLicences()
    {
        return $this->licences;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setStrapline($strapline)
    {
        $this->strapline = $strapline;
    }

    public function getStrapline()
    {
        return $this->strapline;
    }


    public function getDisplayDomain()
    {
        if ($this->domain && strpos($this->domain, '.') !== 2) {
            return 'http://www.' . rtrim($this->domain, '/');
        } elseif ($this->domain) {
            return 'http://' . rtrim($this->domain, '/');
        }

        return $this->domain;

    }

    public function getSessionNamespace()
    {
        return preg_replace('/[^a-z0-9A-Z]/', '', $this->getDomain());
    }

    /**
     * Get filter query
     *
     * @param $targetTableAlias
     *
     * @return string
     */
    public function getSiteFilterQuery($targetTableAlias)
    {
        $parent = $this->getParent();
        if ($parent && $parentId = $parent->getId()) {
            return $targetTableAlias . '.site_id = ' . $parentId;
        } elseif ($id = $this->getId()) {
            return $targetTableAlias . '.site_id = ' . $id;
        }

        return '';
    }
}