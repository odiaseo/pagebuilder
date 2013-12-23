<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use PageBuilder\BaseEntity;

/**
 * Product Template
 *
 * @ORM\Entity
 * @ORM\Table(name="Template")
 *
 */
class Template extends BaseEntity
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
     * @var \datetime createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    protected $createdAt;
    /**
     * @var \datetime updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    protected $updatedAt;
    /**
     * @ORM\ManyToOne(targetEntity="Site", cascade="persist")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    protected $siteId;

    public function __construct()
    {
        $this->templateSections = new ArrayCollection();
    }

    /**
     * @param \datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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

    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    public function getSiteId()
    {
        return $this->siteId;
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

    /**
     * @param \datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}