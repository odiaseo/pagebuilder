<?php
namespace PageBuilder\Entity\Join;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use PageBuilder\BaseEntity;

/**
 * Template Section Join table
 *
 * @ORM\Entity
 * @ORM\Table(name="Template_Section", uniqueConstraints={@ORM\UniqueConstraint(name="tsx_idx", columns={"template_id", "section_id"})})
 *
 */
class TemplateSection extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Template", inversedBy="templateSections", cascade={"all"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $templateId;
    /**
     * @ORM\Column(type="integer", name="sort_order")
     */
    protected $sortOrder = 0;
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Section", inversedBy="templates", cascade={"all"})
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=false)
     */
    protected $sectionId;
    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive = 1;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     * @return \PageBuilder\Entity\Section mixed
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    public function getTemplateId()
    {
        return $this->templateId;
    }


}