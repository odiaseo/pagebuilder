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
 *
 */
class Theme
    extends CommonBaseEntity
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $thumbnail;
    /**
     * Place holder for the association
     *
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\SiteTheme", mappedBy="themeId", fetch="LAZY")
     */
    protected $siteThemes;
    /**
     * Place holder for the association
     *
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\PageTheme", mappedBy="themeId", fetch="LAZY")
     */
    protected $pageThemes;
    /**
     * Place holder for the association
     *
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
        $this->pageThemes = new ArrayCollection();
        $this->templates  = new ArrayCollection();
    }

    public function setBootstrapVersion($bootstrapVersion)
    {
        $this->bootstrapVersion = $bootstrapVersion;
    }

    public function getBootstrapVersion()
    {
        return $this->bootstrapVersion;
    }

    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    public function getTemplates()
    {
        return $this->templates;
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

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPageThemes($pageThemes)
    {
        $this->pageThemes = $pageThemes;
    }

    public function getPageThemes()
    {
        return $this->pageThemes;
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

}
