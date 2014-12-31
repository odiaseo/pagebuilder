<?php
namespace PageBuilder\Model;

use Doctrine\ORM\AbstractQuery;

/**
 * Class ResourceModel
 *
 * @package PageModel\Model
 */
class ResourceModel extends BaseModel {
	/**
	 * List Generic Resources
	 *
	 * @param int $limit
	 * @param int $mode
	 *
	 * @return mixed
	 */
	public function getGenericResources( $limit = 10, $mode = AbstractQuery::HYDRATE_OBJECT ) {
		$query = $this->getEntityManager()
		              ->createQueryBuilder()
		              ->select( 'e' )
		              ->from( $this->_entity, 'e' )
		              ->where( 'e.isGeneric = 1' )
		              ->setMaxResults( $limit )
		              ->getQuery();

		return $query->execute( $mode );
	}
}
