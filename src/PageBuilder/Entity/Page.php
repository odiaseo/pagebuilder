<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BasePage;


/**
 * Page
 *
 * @ORM\Entity(repositoryClass="PageBuilder\Model\PageRepository")
 * @ORM\Table(name="Page")
 * @Gedmo\Tree(type="nested")
 * @Gedmo\TranslationEntity(class="PageBuilder\Entity\PageTranslation")
 */
class Page
    extends BasePage
{
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=true)
     */
    protected $template;
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Page", inversedBy="children", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Page", mappedBy="parent")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    protected $children;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\PageTheme", mappedBy="pageId" , cascade={"persist"})
     * @ORM\JoinTable(name="Page_Theme")
     */
    protected $pageThemes;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Site", mappedBy="rootPage")
     */
    protected $sites;
    /**
     * @ORM\OneToMany(
     *   targetEntity="PageTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    public function __construct()
    {
        parent::__construct();
        $this->children     = new ArrayCollection();
        $this->pageThemes   = new ArrayCollection();
        $this->sites        = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }


    public function addTranslation(PageTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
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

    public function setPageThemes($pageThemes)
    {
        $this->pageThemes = $pageThemes;
    }

    public function getPageThemes()
    {
        return $this->pageThemes;
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