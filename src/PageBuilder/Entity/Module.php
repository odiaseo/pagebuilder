<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\AbstractEntity;

/**
 * Module
 *
 * @ORM\Entity
 *
 */
class Module
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
     * @ORM\Column(type="string")
     */
    protected $description = '';
    /**
     * @ORM\Column(type="string")
     */
    protected $label;
    /**
     * @ORM\Column(type="string")
     */
    protected $thumbnail;
    /**
     * @ORM\Column(type="smallint", name="sort_order")
     */
    protected $sortOrder;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Licence")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id")
     */
    protected $licenceId;


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

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLicenceId($licenceId)
    {
        $this->licenceId = $licenceId;
    }

    public function getLicenceId()
    {
        return $this->licenceId;
    }

    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
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