<?php
namespace PageBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseSite;

/**
 * A Site.
 *
 * @ORM\Entity
 * @ORM\Table(name="Site")
 */
class Site
    extends BaseSite
{
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\SiteTheme", mappedBy="siteId", cascade="persist")
     */
    private $siteThemes;
    /**
     * @ORM\ManyToMany(targetEntity="PageBuilder\Entity\Licence", cascade="persist")
     * @ORM\JoinTable(name="Site_Licence")
     */
    private $licences;

    public function __construct()
    {
        $this->licences = new ArrayCollection();
    }

    public function setSiteThemes($siteThemes)
    {
        $this->siteThemes = $siteThemes;
    }

    public function getSiteThemes()
    {
        return $this->siteThemes;
    }

    public function setLicences($licences)
    {
        $this->licences = $licences;
    }

    public function getLicences()
    {
        return $this->licences;
    }


}