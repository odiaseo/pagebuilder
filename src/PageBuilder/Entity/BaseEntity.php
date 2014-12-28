<?php
namespace PageBuilder\Entity;

use Doctrine\ORM\Mapping as ORM;
use SynergyCommon\Entity\BaseEntity as CommonEntity;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseEntity extends CommonEntity {
	/**
	 * @ORM\ManyToOne(targetEntity="PageBuilder\Entity\Site", cascade="persist")
	 * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
	 */
	protected $site;

	public function setSite( $site ) {
		$this->site = $site;
	}

	public function getSite() {
		return $this->site;
	}
}
