<?php
namespace PageBuilder\Model;


use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use SynergyCommon\Model\AbstractModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceManager;

class BaseModel
    extends AbstractModel
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $_em;

    /** @var \SynergyCommon\Entity\BaseEntity */
    protected $_entityInstance;

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
     * @return \SynergyCommon\Entity\BaseEntity
     */
    public function getEntityInstance()
    {
        return $this->_entityInstance;
    }
}