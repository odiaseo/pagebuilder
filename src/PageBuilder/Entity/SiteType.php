<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * SiteType
 *
 * @ORM\Entity
 * @ORM\Table(name="Site_Type")
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class SiteType extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $title;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    /**
     * @ORM\Column(type="string", nullable=true, name="facebook_page")
     */
    protected $facebookPage;
    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Site", mappedBy="siteType")
     */
    protected $sites;
    /**
     * @ORM\Column(type="string", length=50, name="app_id")
     */
    protected $appId;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getFacebookPage()
    {
        return $this->facebookPage;
    }

    /**
     * @param mixed $facebookPage
     */
    public function setFacebookPage($facebookPage)
    {
        $this->facebookPage = $facebookPage;
    }

    /**
     * @return mixed
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @param mixed $sites
     */
    public function setSites($sites)
    {
        $this->sites = $sites;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
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
