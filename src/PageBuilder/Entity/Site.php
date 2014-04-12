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
     * @ORM\Column(type="boolean", name="is_subdomain")
     */
    protected $isSubdomain;
    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $description;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $languages;
    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $timezone = 'Europe/London';

    public function __construct()
    {
        $this->licences   = new ArrayCollection();
        $this->settings   = new ArrayCollection();
        $this->siteThemes = new ArrayCollection();
        $this->modules    = new ArrayCollection();
    }

    public function setIsSubdomain($isSubdomain)
    {
        $this->isSubdomain = $isSubdomain;
    }

    public function getIsSubdomain()
    {
        return $this->isSubdomain;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    public function getLanguages()
    {
        return $this->languages;
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
}