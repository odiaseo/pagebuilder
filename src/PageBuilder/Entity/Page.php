<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BasePage;

/**
 * Page
 * @ORM\Entity(repositoryClass="PageBuilder\Model\PageRepository")
 * @ORM\Table(name="Page", indexes={@ORM\Index(name="IDX_LFT_RGT_ROOT",
 * columns={"lft", "rgt", "root"})})
 * @Gedmo\Tree(type="nested")
 * @Gedmo\TranslationEntity(class="PageBuilder\Entity\PageTranslation")
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class Page extends BasePage
{
    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=true)
     */
    protected $template;

    /**
     * @ORM\Cache("READ_ONLY")
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Page", inversedBy="children", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Page", mappedBy="parent", fetch="LAZY")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\PageTemplate", mappedBy="page" , cascade={"persist"})
     * @ORM\JoinTable(name="Page_Template")
     */
    protected $pageTemplates;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToMany(targetEntity="Resource", cascade="persist", fetch="LAZY")
     * @ORM\JoinTable(name="Page_Resource")
     */
    protected $resources;

    /**
     * @ORM\Column(type="string", length=120, nullable=true, name="js_file")
     */
    protected $jsFile = 'frontend';

    /**
     * @ORM\Column(type="string", length=120, nullable=true, name="css_file")
     */
    protected $cssFile = 'frontend';

    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Site", mappedBy="rootPage", fetch="LAZY")
     */
    protected $sites;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(
     *   targetEntity="PageTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"},
     *   fetch="LAZY"
     * )
     */
    protected $translations;

    public function __construct()
    {
        $this->children     = new ArrayCollection();
        $this->sites        = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->resources    = new ArrayCollection();
    }

    public function addTranslation(PageTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }

    /**
     * @return mixed
     */
    public function getJsFile()
    {
        return $this->jsFile;
    }

    /**
     * @param mixed $jsFile
     */
    public function setJsFile($jsFile)
    {
        $this->jsFile = $jsFile;
    }

    /**
     * @return mixed
     */
    public function getCssFile()
    {
        return $this->cssFile;
    }

    /**
     * @param mixed $cssFile
     */
    public function setCssFile($cssFile)
    {
        $this->cssFile = $cssFile;
    }

    /**
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @param mixed $resources
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param mixed $translations
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    public function setSites($sites)
    {
        $this->sites = $sites;
    }

    public function getSites()
    {
        return $this->sites;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \PageBuilder\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return \PageBuilder\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
