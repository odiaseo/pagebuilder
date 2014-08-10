<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Component
 *
 * @ORM\Entity
 * @ORM\Table(name="Component")
 *
 */
class Component
    extends BaseEntity

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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $content;
    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", name="cssid")
     */
    protected $cssId;
    /**
     * @ORM\Column(type="string", nullable=true, name="css_class")
     */
    protected $cssClass;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }


    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }

    public function getCssClass()
    {
        return $this->cssClass;
    }

    public function setCssId($cssId)
    {
        $this->cssId = $cssId;
    }

    public function getCssId()
    {
        return $this->cssId;
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

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

}