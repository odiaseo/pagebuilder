<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseEntity as CommonEntity;

/**
 * Alexa Ranking By Day
 *
 * @ORM\Entity
 * @ORM\Table(name="Site_Rank",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="site_ranked_at", columns={
 *         "site_id", "ranked_at"
 *     })})
 * @ORM\HasLifecycleCallbacks
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class SiteRank extends CommonEntity
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
     * @ORM\Column(type="integer")
     */
    protected $popularity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="ranked_at")
     */
    protected $rankedAt;

    /**
     * @ORM\Cache("READ_ONLY")
     * @ORM\ManyToOne(targetEntity="Site", cascade="persist", inversedBy="siteRanks", fetch="LAZY")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    protected $site;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $identifier
     */
    public function setId($identifier)
    {
        $this->id = $identifier;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getPopularity()
    {
        return $this->popularity;
    }

    /**
     * @param mixed $popularity
     */
    public function setPopularity($popularity)
    {
        $this->popularity = $popularity;
    }

    /**
     * @return mixed
     */
    public function getRankedAt()
    {
        return $this->rankedAt;
    }

    /**
     * @param mixed $rankedAt
     */
    public function setRankedAt($rankedAt)
    {
        $this->rankedAt = $rankedAt;
    }
}
