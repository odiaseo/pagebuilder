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

    protected function disableSiteFilter()
    {
        /** @var Site $site */
        if ($this->getServiceLocator()->has('active\site')) {
            $site      = $this->getServiceLocator()->get('active\Site');
            $namespace = $site->getSessionNamespace();
            (new Container($namespace))->offsetSet(self::FILTER_SESSION_KEY, true);
        }
    }
}
