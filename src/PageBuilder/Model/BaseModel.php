<?php
namespace PageBuilder\Model;

use PageBuilder\Entity\Site;
use SynergyCommon\Model\AbstractModel;
use Zend\Session\Container;

/**
 * Class BaseModel
 *
 * @package PageBuilder\Model
 */
class BaseModel extends AbstractModel
{
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

    /**
     * @return bool
     */
    public function disableSiteFilter()
    {
        /** @var Site $site */
        if ($this->getServiceLocator()->has('active\site')) {
            $site      = $this->getServiceLocator()->get('active\site');
            $namespace = $site->getSessionNamespace();
            $container = new Container($namespace);

            return $container->offsetSet(self::FILTER_SESSION_KEY, 1);
        }

        return false;
    }
}
