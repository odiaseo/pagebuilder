<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity as CommonBaseEntity;

/**
 * Themes
 *
 * @ORM\Entity
 * @ORM\Table(name="Theme")
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class Theme extends CommonBaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $bootstrapVersion = 2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description = '';

    /**
     * @ORM\Column(name="wrapper_class",type="string", length=30, nullable=true)
     */
    protected $wrapperClass;

    /**
     * @ORM\Column(name="wrapper",type="string", length=30, nullable=true)
     */
    protected $wrapper = 'pageTop';

    /**
     * @ORM\Column(name="body_class",type="string", length=30, nullable=true)
     */
    protected $bodyClass;

    /**
     * @ORM\Column(name="background_image",type="string", nullable=true)
     */
    protected $backgroundImage;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $thumbnail;

    /**
     * Place holder for the association
     *
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\SiteTheme", mappedBy="themeId", fetch="LAZY")
     */
    protected $siteThemes;

    /**
     * Place holder for the association
     *
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Template", mappedBy="theme")
     */
    protected $templates;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", nullable=false, length=25)
     */
    protected $folder = '';

    public function __construct()
    {
        $this->siteThemes = new ArrayCollection();
        $this->templates  = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * @param mixed $wrapper
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
    }

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    public function setBootstrapVersion($bootstrapVersion)
    {
        $this->bootstrapVersion = $bootstrapVersion;
    }

    public function getBootstrapVersion()
    {
        return $this->bootstrapVersion;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function getFolder()
    {
        return $this->folder;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($itemId)
    {
        $this->id = $itemId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSiteThemes($siteThemes)
    {
        $this->siteThemes = $siteThemes;
    }

    public function getSiteThemes()
    {
        return $this->siteThemes;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImage()
    {
        return $this->backgroundImage;
    }

    /**
     * @param mixed $imageBackground
     */
    public function setBackgroundImage($imageBackground)
    {
        $this->backgroundImage = $imageBackground;
    }

    /**
     * @return mixed
     */
    public function getWrapperClass()
    {
        return $this->wrapperClass;
    }

    /**
     * @param mixed $isWrapped
     */
    public function setWrapperClass($isWrapped)
    {
        $this->wrapperClass = $isWrapped;
    }

    /**
     * @return mixed
     */
    public function getBodyClass()
    {
        return $this->bodyClass;
    }

    /**
     * @param mixed $bodyClass
     */
    public function setBodyClass($bodyClass)
    {
        $this->bodyClass = $bodyClass;
    }

    /**
     * @return mixed|string
     */
    public function getActiveColourScheme()
    {
        /** @var Join\SiteTheme $siteTheme */
        if (!$this->getSiteThemes()) {
            return '';
        }

        foreach ($this->getSiteThemes() as $siteTheme) {
            if ($siteTheme->getIsActive()) {
                return $siteTheme->getColourScheme();
            }
        }

        return '';
    }
}
