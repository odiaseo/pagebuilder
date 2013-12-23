<?php
namespace PageBuilder\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use PageBuilder\Exception\Exception;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceManager;

class BaseModel
{
    /** @var string */
    protected $orm = 'orm_default';
    /** @var \Doctrine\ORM\EntityManager */
    protected $_em;
    /** @var string */
    protected $_entity;
    /** @var \PageBuilder\BaseEntity */
    protected $_entityInstance;

    public function setEntity($entity)
    {
        $this->_entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }


    public function  getEntityManager()
    {
        return $this->_em;
    }

    public function setEntityManager($em)
    {
        $this->_em = $em;
    }

    /**
     * Find object by id in repository
     *
     * @param int @id id of an object
     *
     * @return \Doctrine\ORM\Mapping\Entity
     */
    public function findObject($id = 0)
    {
        return $this->_em->getRepository($this->_entity)->find($id);
    }

    /**
     * Remove record by id
     *
     * @param int $id
     *
     * @return bool
     * @throws \Exception
     */
    public function remove($id = 0)
    {
        $retv = false;
        if ($id) {
            $object = $this->findObject($id);
            if ($object) {

                $this->getEntityManager()->remove($object);
                $this->getEntityManager()->flush();
                $retv = true;

            }
        }

        return $retv;
    }

    /**
     * Save given entity
     *
     * @param $entity
     *
     * @return mixed
     * @throws \Exception
     */
    public function save($entity)
    {

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();


        return $entity;
    }

    /**
     * Since Doctrine closes the EntityManager after a Exception, we have to create
     * a fresh copy (so it is possible to save logs in the current request)
     *
     * @return void
     */
    private function recoverEntityManager()
    {
        $this->setEntityManager(
            EntityManager::create(
                $this->getEntityManager()->getConnection(),
                $this->getEntityManager()->getConfiguration()
            )
        );
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->_em->getRepository($this->getEntity());
    }

    public function listItemsByTitle()
    {

        $list = array();
        $qb   = $this->_em->createQueryBuilder();
        /** @var $query \Doctrine\ORM\Query */
        $query = $qb->select('e.id, e.title, e.description')
            ->from($this->_entity, 'e')
            ->getQuery();

        $result = $query->execute(array(), AbstractQuery::HYDRATE_ARRAY);

        if ($result) {
            foreach ($result as $item) {
                $list[$item['id']] = array(
                    'title'       => $item['title'],
                    'description' => $item['description']
                );
            }

        }

        return $list;

    }

    public static function getUniqueGridIdentifier(array $options)
    {
        return implode('_', array_filter($options));
    }

    public function __call($method, $args)
    {
        $repo = $this->getRepository($this->getEntity());
        try {
            return \call_user_func_array(array($repo, $method), $args);
        } catch (Exception $e) {
            throw new Exception("Unable to execute method {$method} - " . $e->getMessage());
        }
    }


    public function getEntityIdBySlug($data)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e.id')
            ->from($this->_entity, 'e')
            ->where('e.slug = :slug')
            ->setParameter(':slug', $data)
            ->setMaxResults(1)
            ->getQuery();

        try {
            $id = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $id = null;
        }

        return $id;
    }

    public function getEntityLikeSlug($data)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e.id')
            ->from($this->_entity, 'e')
            ->where('e.slug LIKE :slug')
            ->setParameter(':slug', "{$data}%")
            ->setMaxResults(1)
            ->getQuery();

        try {
            $id = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $id = null;
        }

        return $id;
    }

    public function getItemListByIds(array $offerIds)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $query = $qb
            ->select('e')
            ->from($this->_entity, 'e')
            ->where($qb->expr()->in('e.id', $offerIds));

        return $query->getQuery()->execute(null, AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * Returns a subset of fields based on the criteria
     *
     * @param       $idField
     * @param       $idValue
     * @param array $returnFields
     * @param null  $limit
     *
     * @return array|mixed
     */
    public function getFieldsBy($idField, $idValue, array $returnFields, $limit = null)
    {
        $select = array();
        foreach ($returnFields as $f) {
            $select[] = 'e.' . $f;
        }

        $query = $this->_em
            ->createQueryBuilder()
            ->select(implode(',', $select))
            ->from($this->_entity, 'e')
            ->where("e.$idField = :val")
            ->setParameter(":val", $idValue)
            ->getQuery();

        if ($limit == 1) {
            try {
                return $query->getSingleResult();
            } catch (NoResultException $e) {
                return null;
            }
        } elseif ($limit) {
            $query->setMaxResults($limit);

            return $query->getArrayResult();
        }

        return array();
    }


    /**
     * Update and foreign entity
     *
     * @param $id
     * @param $param
     * @param $value
     *
     * @return \Doctrine\ORM\Mapping\Entity
     */
    public function updateForeignEntity($id, $param, $value)
    {
        $entity  = $this->getRepository($this->_entity)->find($id);
        $mapping = $this->getEntityManager()->getClassMetadata($this->_entity);
        $target  = $mapping->associationMappings[$param]['targetEntity'];

        /** @var $collection ArrayCollection */
        $collection = $entity->$param;
        if ($collection) {
            $collection->clear();
        } else {
            $collection = new ArrayCollection();
        }
        $value = array_unique(array_filter($value));

        foreach ($value as $v) {
            if ($foreignEntity = $this->getEntityManager()->find($target, $v)) {
                $collection->add($foreignEntity);
            }
        }

        $entity->$param = $collection;

        return $this->save($entity);
    }

    /**
     * Update and entity with data in params
     *
     * @param       $entityId
     * @param array $params
     *
     * @return array
     */
    public function updateEntity($entityId, array $params)
    {
        $error   = false;
        $message = '';

        $entity = $this->getRepository($this->getEntity())->find($entityId);

        $mapping = $this->getEntityManager()->getClassMetadata($this->_entity);

        try {

            foreach ($params as $param => $value) {
                if (array_key_exists($param, $mapping->fieldMappings)
                    or array_key_exists($param, $mapping->associationMappings)
                ) {
                    $method = 'set' . ucfirst($param);
                    if (isset($mapping->fieldMappings[$param])) {
                        $type = $mapping->fieldMappings[$param]['type'];
                        if ($type == 'datetime' || $type == 'date') {
                            $value = $value ? new \DateTime($value) : null;
                        }
                        $entity->$method($value);

                    } elseif (isset($mapping->associationMappings[$param])) {
                        $target = $mapping->associationMappings[$param]['targetEntity'];

                        /** @var $collection ArrayCollection */
                        $collection = $entity->$param;
                        if ($mapping->associationMappings[$param]['type'] == 8) {
                            $collection->clear();
                            $value = explode(',', $value);
                            $value = array_unique(array_filter($value));

                            foreach ($value as $v) {
                                if ($foreignEntity = $this->getEntityManager()->find($target, $v)) {
                                    $collection->add($foreignEntity);
                                }
                            }
                            $entity->$param = $collection;
                        } else {
                            if (is_numeric($value)) {
                                $foreignEntity = $this->getEntityManager()->find($target, $value);
                            } else {
                                $foreignEntity = $this->getEntityManager()
                                    ->getRepository($target)
                                    ->findOneBy(array('slug' => $value));
                            }

                            $entity->$method($foreignEntity);
                        }

                    }
                }
            }

            if (!$error) {
                return $this->save($entity);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error   = true;
        }

        return array($error, $message);
    }

    public function getOrm()
    {
        return $this->orm;
    }

    /**
     * @param $entityInstance
     *
     * @return $this
     */
    public function setEntityInstance($entityInstance)
    {
        $this->_entityInstance = $entityInstance;

        return $this;
    }

    /**
     * @return \PageBuilder\BaseEntity
     */
    public function getEntityInstance()
    {
        return $this->_entityInstance;
    }


}