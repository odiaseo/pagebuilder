<?php
namespace PageBuilder\Entity;

use SynergyCommon\Entity\BaseEntity as CommonEntity;

abstract class BaseEntity
    extends CommonEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="SynergyCommon\Entity\Site", cascade="persist")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    public function setSite($site)
    {
        $this->site = $site;
    }

    public function getSite()
    {
        return $this->site;
    }

}