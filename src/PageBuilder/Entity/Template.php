<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity as CommonBaseEntity;

/**
 * Product Template
 *
 * @ORM\Entity
 * @ORM\Table(name="Template")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="SlowMoving")
 */
class Template extends CommonBaseEntity
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
     * @ORM\Column(type="string")
     */
    protected $description;
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $layout;
    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\TemplateSection",
     * mappedBy="templateId", cascade={"persist","remove"}, fetch="EAGER")
     * @ORM\JoinTable(name="Template_Section")
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    protected $templateSections;
    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Theme", inversedBy="templates")
     * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", nullable=false)
     */
    protected $theme;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\PageTemplate", mappedBy="template" , cascade={"persist"})
     * @ORM\JoinTable(name="Page_Template")
     */
    protected $templatePages;

    public function __construct()
    {
        $this->templateSections = new ArrayCollection();
        $this->templatePages    = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @return mixed
     */
    public function getTemplatePages()
    {
        return $this->templatePages;
    }

    /**
     * @param mixed $templatePages
     */
    public function setTemplatePages($templatePages)
    {
        $this->templatePages = $templatePages;
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

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setTemplateSections($templateSections)
    {
        $this->templateSections = $templateSections;
    }

    public function getTemplateSections()
    {
        return $this->templateSections;
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
