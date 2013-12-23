<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use PageBuilder\BaseEntity;

/**
 * A Site.
 *
 * @ORM\Entity
 * @ORM\Table(name="Site")
 * @ORM\HasLifecycleCallbacks
 */
class Site extends BaseEntity
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
     * @ORM\Column(type="string")
     */
    protected $keyword;
    /**
     * @ORM\Column(type="string")
     */
    protected $logo;
    /**
     * @ORM\Column(type="string")
     */
    protected $domain;
    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive;
    /**
     * @var \datetime createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\SiteTheme", mappedBy="siteId")
     */
    protected $siteThemes;

    /**
     * @var \Datetime updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updatedAt;
}