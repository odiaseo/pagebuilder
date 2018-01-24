<?php

namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use SynergyCommon\Entity\BaseSite;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * A Site.
 *
 * @ORM\Entity
 * @ORM\Table(name="Site", indexes={
 *     @ORM\Index(name="IDX_DOMAIN", columns={"domain", "is_active"}),
 *     @ORM\Index(name="IDX_ACT_TYP_ADMIN", columns={"is_active","site_type_id", "is_admin"}),
 *     @ORM\Index(name="IDX_TYP_PGE_DOM", columns={"site_type_id","root_id", "is_active"})
 *     })
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class Site extends BaseSite
{
    /**
     * @ORM\Column(type="string", length=75, nullable=true, name="display_title")
     */
    protected $displayTitle;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    protected $strapline;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    protected $currency;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\SiteTheme", mappedBy="siteId", cascade="persist")
     */
    protected $siteThemes;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToMany(targetEntity="PageBuilder\Entity\Setting", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinTable(name="Site_Setting")
     */
    protected $settings;


    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Site", mappedBy="parent", cascade="persist", fetch="LAZY")
     */
    protected $subDomains;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToMany(targetEntity="PageBuilder\Entity\Site", fetch="LAZY")
     * @ORM\JoinTable(name="Site_Linked_Site",
     *      joinColumns={@ORM\JoinColumn(name="main_site_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sub_site_id", referencedColumnName="id")}
     *      ))
     */
    protected $linkedSites;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", inversedBy="subDomains", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Page", cascade="persist", inversedBy="sites")
     * @ORM\JoinColumn(name="root_id", referencedColumnName="id", nullable=false)
     */
    protected $rootPage;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\SiteType", cascade="persist", inversedBy="sites", fetch="LAZY")
     * @ORM\JoinColumn(name="site_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siteType;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, name="default_timezone")
     */
    protected $defaultTimezone = 'Europe/London';

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
     */
    protected $defaultTemplate;

    /**
     * @ORM\Column(type="boolean", name="is_admin", nullable=true, options={"default" = 0})
     */
    protected $isAdmin = 0;

    /**
     * Inverse Side
     *
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToMany(targetEntity="Redirect", mappedBy="sites")
     */
    protected $redirects;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="SiteRank", mappedBy="site", cascade="persist")
     */
    protected $siteRanks;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="voucher_site_id", referencedColumnName="id", nullable=true)
     */
    protected $voucherSite;

    public function __construct()
    {
        $this->settings    = new ArrayCollection();
        $this->siteThemes  = new ArrayCollection();
        $this->subDomains  = new ArrayCollection();
        $this->linkedSites = new ArrayCollection();
        $this->redirects   = new ArrayCollection();
        $this->siteRanks   = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getVoucherSite()
    {
        return $this->voucherSite;
    }

    /**
     * @param mixed $voucherSite
     */
    public function setVoucherSite($voucherSite)
    {
        $this->voucherSite = $voucherSite;
    }

    /**
     * @return mixed
     */
    public function getSiteRanks()
    {
        return $this->siteRanks;
    }

    /**
     * @param mixed $siteRanks
     */
    public function setSiteRanks($siteRanks)
    {
        $this->siteRanks = $siteRanks;
    }

    /**
     * @return mixed
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param mixed $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return mixed
     */
    public function getRedirects()
    {
        return $this->redirects;
    }

    /**
     * @param mixed $redirects
     */
    public function setRedirects($redirects)
    {
        $this->redirects = $redirects;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getLinkedSites()
    {
        return $this->linkedSites;
    }

    /**
     * @param mixed $linkedSites
     */
    public function setLinkedSites($linkedSites)
    {
        $this->linkedSites = $linkedSites;
    }

    public function setSiteType($siteType)
    {
        $this->siteType = $siteType;
    }

    /**
     * @return SiteType
     */
    public function getSiteType()
    {
        return $this->siteType;
    }

    public function setDefaultTemplate($defaultTemplate)
    {
        $this->defaultTemplate = $defaultTemplate;
    }

    public function getDefaultTemplate()
    {
        return $this->defaultTemplate;
    }

    public function setRootPage($rootPage)
    {
        $this->rootPage = $rootPage;
    }

    public function getRootPage()
    {
        return $this->rootPage;
    }

    public function setDisplayTitle($displayTitle)
    {
        $this->displayTitle = $displayTitle;
    }

    public function getDisplayTitle()
    {
        return $this->displayTitle;
    }

    public function getLocalisedTitle()
    {
        return $this->displayTitle . ' ' . \Locale::getDisplayRegion(
                $this->getLocale()
            );
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

    public function setSiteThemes($siteThemes)
    {
        $this->siteThemes = $siteThemes;
    }

    public function getSiteThemes()
    {
        return $this->siteThemes;
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
        $scheme = $this->getIsSecure() ? 'https' : 'http';

        if ($this->getIsSubDomain()) {
            return $scheme . '://' . rtrim($this->domain, '/');
        } else {
            return $scheme . '://www.' . rtrim($this->domain, '/');
        }
    }

    public function getSessionNamespace()
    {
        return 'sess' . preg_replace('/[^a-z0-9A-Z]/', '', $this->getDomain());
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
        if ($parent and $parentId = $parent->getId()) {
            return $targetTableAlias . '.site_id = ' . $parentId;
        } elseif ($id = $this->getId()) {
            return $targetTableAlias . '.site_id = ' . $id;
        }

        return '';
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function ensureIpAddressIsSet()
    {
        if (empty($this->ipAddress) and $this->domain) {
            $ipAddress = gethostbyname($this->domain);
            if ($this->domain != $ipAddress) {
                $this->setIpAddress($ipAddress);
            }
        }
    }

    /**
     * @return array
     */
    public function getAllowedSites()
    {
        if ($this->getIsAdmin()) {
            return [];
        }

        $ids[] = $this->getId();
        if ($this->getSubDomains() and $this->getSubDomains()->count()) {
            $subDomains = $this->getSubDomains();
            foreach ($subDomains as $subSite) {
                $ids[] = $subSite->getId();
            }
        }

        if ($this->getLinkedSites() and $this->getLinkedSites()->count()) {
            $linkedSites = $this->getLinkedSites();
            /** @var Site $subSite */
            foreach ($linkedSites as $subSite) {
                $ids[] = $subSite->getId();
            }
        }

        if ($this->getParent()) {
            $ids[] = $this->getParent()->getId();
        }

        $ids = array_unique($ids);

        sort($ids);

        return $ids;
    }

    /**
     * Get sum of all offers
     *
     * @return mixed
     */
    public function sumOfferCount()
    {
        $count = $this->getOfferCount();
        if ($this->getParent()) {
            $count += $this->getParent()->getOfferCount();
        }

        if ($this->getLinkedSites()->count()) {
            /** @var Site $linked */
            foreach ($this->getLinkedSites() as $linked) {
                $count += $linked->getOfferCount();
            }
        }

        if ($this->getSubDomains()->count()) {
            foreach ($this->getSubDomains() as $linked) {
                $count += $linked->getOfferCount();
            }
        }

        return $count;
    }
}
