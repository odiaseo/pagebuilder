<?php
namespace PageBuilder\Model;

use SynergyCommon\Model\AbstractModel;

/**
 * Class BaseModel
 *
 * @package PageBuilder\Model
 */
class BaseModel extends AbstractModel {
	/** @var \SynergyCommon\Entity\BaseEntity */
	protected $_entityInstance;

	/**
	 * @param $entityInstance
	 *
	 * @return $this
	 */
	public function setEntityInstance( $entityInstance ) {
		$this->_entityInstance = $entityInstance;

		return $this;
	}

	/**
	 * @return \SynergyCommon\Entity\BaseEntity
	 */
	public function getEntityInstance() {
		return $this->_entityInstance;
	}
}
