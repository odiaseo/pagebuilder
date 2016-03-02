<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * Redirect
 *
 * @ORM\Entity
 * @ORM\Table(name="Redirect")
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class Redirect extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string",  length=149)
     */
    protected $source;
    /**
     * @ORM\Column(type="string",  length=149, name="redirect_destination")
     */
    protected $redirectDestination;
    /**
     * @ORM\Column(type="smallint", name="redirect_type")
     */
    protected $redirectType;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Site", cascade="persist", fetch="EAGER", inversedBy="redirects")
     * @ORM\JoinTable(name="Redirect_Site")
     */
    protected $sites;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
    }

    public function setRedirectDestination($destination)
    {
        $this->redirectDestination = $destination;
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
    public function getSites()
    {
        return $this->sites;
    }

    public function getRedirectDestination()
    {
        return $this->redirectDestination;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRedirectType($redirectType)
    {
        $this->redirectType = $redirectType;
    }

    public function getRedirectType()
    {
        return $this->redirectType;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }
}
