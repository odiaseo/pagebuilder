<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * Section
 *
 * @ORM\Table(name="Section")
 * @ORM\Entity
 *
 */
class Section
    extends AbstractEntity
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
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string")
     */
    protected $slug;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\TemplateSection", mappedBy="sectionId", cascade={"persist","remove"}, fetch="EAGER")
     * @ORM\JoinTable(name="Template_Section")
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    protected $templates;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    public function getTemplates()
    {
        return $this->templates;
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