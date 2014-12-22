<?php
namespace PageBuilder\Model;

use SynergyCommon\Model\AbstractModel;

/**
 * Class ResourceModel
 *
 * @package PageModel\Model
 */
class ResourceModel extends AbstractModel {
	/**
	 * List Generic Resources
	 *
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function getGenericResources( $limit = 10 ) {
		$query = $this->getEntityManager()
		              ->createQueryBuilder()
		              ->select( 'e' )
		              ->from( $this->_entity, 'e' )
		              ->where( 'e.isGeneric = 1' )
		              ->setMaxResults( $limit )
		              ->getQuery();

		return $query->execute();
	}
}
