<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use SynergyCommon\Entity\BaseSite;

/**
 * A Site.
 *
 * @ORM\Entity
 * @ORM\Table(name="Site")
 * @ORM\HasLifecycleCallbacks
 */
class Site
    extends BaseSite
{
    /**
     * @ORM\OneToMany(targetEntity="PageBuilder\Entity\Join\SiteTheme", mappedBy="siteId", cascade="persist")
     */
    protected $siteThemes;

    public function setSiteThemes($siteThemes)
    {
        $this->siteThemes = $siteThemes;
    }

    public function getSiteThemes()
    {
        return $this->siteThemes;
    }


}