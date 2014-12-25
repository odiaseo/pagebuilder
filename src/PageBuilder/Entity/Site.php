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
class Site extends BaseSite {
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
	 * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Page", cascade="persist", inversedBy="sites")
	 * @ORM\JoinColumn(name="root_id", referencedColumnName="id", nullable=false)
	 */
	private $rootPage;
	/**
	 * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\SiteType", cascade="persist", inversedBy="sites", fetch="EAGER")
	 * @ORM\JoinColumn(name="site_type_id", referencedColumnName="id", nullable=false)
	 */
	private $siteType;
	/**
	 * @ORM\Column(type="string", length=25, nullable=true, name="default_timezone")
	 */
	private $defaultTimezone = 'Europe/London';
	/**
	 * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Template")
	 * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
	 */
	protected $defaultTemplate;

	public function __construct() {
		$this->licences   = new ArrayCollection();
		$this->settings   = new ArrayCollection();
		$this->siteThemes = new ArrayCollection();
		$this->modules    = new ArrayCollection();
		$this->subDomains = new ArrayCollection();

	}

	public function setSiteType( $siteType ) {
		$this->siteType = $siteType;
	}

	/**
	 * @return SiteType
	 */
	public function getSiteType() {
		return $this->siteType;
	}

	public function setDefaultTemplate( $defaultTemplate ) {
		$this->defaultTemplate = $defaultTemplate;
	}

	public function getDefaultTemplate() {
		return $this->defaultTemplate;
	}

	public function setRootPage( $rootPage ) {
		$this->rootPage = $rootPage;
	}

	public function getRootPage() {
		return $this->rootPage;
	}

	public function setDisplayTitle( $displayTitle ) {
		$this->displayTitle = $displayTitle;
	}

	public function getDisplayTitle() {
		return $this->displayTitle;
	}

	public function getLocalisedTitle() {
		return $this->displayTitle . ' ' . \Locale::getDisplayRegion( $this->getLocale() );
	}

	public function setParent( $parent ) {
		$this->parent = $parent;
	}

	/**
	 * @return Site
	 */
	public function getParent() {
		return $this->parent;
	}

	public function setSubDomains( $subDomains ) {
		$this->subDomains = $subDomains;
	}

	public function getSubDomains() {
		return $this->subDomains;
	}

	public function setDefaultTimezone( $timezone ) {
		$this->defaultTimezone = $timezone;
	}

	public function getDefaultTimezone() {
		return $this->defaultTimezone;
	}

	public function setModules( $modules ) {
		$this->modules = $modules;
	}

	public function getModules() {
		return $this->modules;
	}

	public function setSiteThemes( $siteThemes ) {
		$this->siteThemes = $siteThemes;
	}

	public function getSiteThemes() {
		return $this->siteThemes;
	}

	public function setLicences( $licences ) {
		$this->licences = $licences;
	}

	public function getLicences() {
		return $this->licences;
	}

	public function setSettings( $settings ) {
		$this->settings = $settings;
	}

	public function getSettings() {
		return $this->settings;
	}

	public function setDescription( $description ) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setStrapline( $strapline ) {
		$this->strapline = $strapline;
	}

	public function getStrapline() {
		return $this->strapline;
	}

	public function getDisplayDomain() {
		if ( $this->getIsSubdomain() ) {
			return 'http://' . rtrim( $this->domain, '/' );
		} else {
			return 'http://www.' . rtrim( $this->domain, '/' );
		}
	}

	public function getSessionNamespace() {
		return preg_replace( '/[^a-z0-9A-Z]/', '', $this->getDomain() );
	}

	/**
	 * Get filter query
	 *
	 * @param $targetTableAlias
	 *
	 * @return string
	 */
	public function getSiteFilterQuery( $targetTableAlias ) {
		$parent = $this->getParent();
		if ( $parent && $parentId = $parent->getId() ) {
			return $targetTableAlias . '.site_id = ' . $parentId;
		} elseif ( $id = $this->getId() ) {
			return $targetTableAlias . '.site_id = ' . $id;
		}

		return '';
	}

	/**
	 * @return array
	 */
	public function getAllowedSites() {
		$ids[] = $this->getId();
		if ( $this->getSubDomains()->count() ) {
			$subDomains = $this->getSubDomains();
			foreach ( $subDomains as $subSite ) {
				$ids[] = $subSite->getId();
			}
		}

		if ( $this->getParent() ) {
			$ids[] = $this->getParent()->getId();
		}

		return $ids;
	}
}
