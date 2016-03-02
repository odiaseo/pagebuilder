<?php
namespace PageBuilder\Entity\Join;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\AbstractEntity;

/**
 * SiteThemes Join table
 *
 * @ORM\Entity
 * @ORM\Table(name="Site_Theme",uniqueConstraints={@ORM\UniqueConstraint(name="THEME_SITE_ACTIVE", columns={"site_id",
 * "theme_id", "is_active"})})
 * @ORM\Cache(usage="READ_ONLY", region="Static")
 */
class SiteTheme
    extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Theme", inversedBy="siteThemes")
     * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", nullable=false)
     */
    protected $themeId;
    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive = 0;
    /**
     * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", inversedBy="siteThemes")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    protected $siteId;

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

    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    public function getSiteId()
    {
        return $this->siteId;
    }

    public function setThemeId($themeId)
    {
        $this->themeId = $themeId;
    }

    public function getThemeId()
    {
        return $this->themeId;
    }
}
