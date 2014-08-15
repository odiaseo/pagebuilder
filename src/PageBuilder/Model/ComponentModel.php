<?php
namespace PageBuilder\Model;

use SynergyCommon\ModelTrait\LocaleAwareTrait;

/**
 * Class ComponentModel
 *
 * @package PageBuilder\Model
 */
class ComponentModel extends BaseModel
{
    use LocaleAwareTrait;

    public function findObject($id)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e')
            ->from($this->_entity, 'e')
            ->where('e.id = :id')
            ->setParameter(':id', $id)
            ->setMaxResults(1)
            ->getQuery();

        $query = $this->addHints($query);

        return $query->getOneOrNullResult();
    }
}
