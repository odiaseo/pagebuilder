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
 *
 */
class Template
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
     * @ORM\Column(type="string")
     */
    protected $description;
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $layout;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\TemplateSection", mappedBy="templateId", cascade={"persist","remove"}, fetch="EAGER")
     * @ORM\JoinTable(name="Template_Section")
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    protected $templateSections;
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Theme", inversedBy="templates")
     * @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
     */
    private $theme;

    public function __construct()
    {
        $this->templateSections = new ArrayCollection();
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    public function getTheme()
    {
        return $this->theme;
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
