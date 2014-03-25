<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BasePage;


/**
 * Page
 *
 * @ORM\Entity(repositoryClass="SynergyCommon\Model\NestedSetRepository")
 * @ORM\Table(name="Page")
 * @Gedmo\Tree(type="nested")
 *
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
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", cascade="persist")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    protected $site;

    public function __construct()
    {
        parent::__construct();
        $this->children   = new ArrayCollection();
        $this->pageThemes = new ArrayCollection();
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setSite($site)
    {
        $this->site = $site;
    }

    public function getSite()
    {
        return $this->site;
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