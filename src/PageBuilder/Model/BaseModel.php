<?php
namespace PageBuilder\Model;

use PageBuilder\Entity\Site;
use SynergyCommon\Model\AbstractModel;
use Zend\Console\Request;

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

    public function disableSiteFilter()
    {
        /** @var Site $site */
        if ($this->getServiceLocator()->has('active\site')) {
            $request = $this->getServiceLocator()->get('request');
            if ($request instanceof Request) {
                $request->getParams()->offsetSet(self::FILTER_SESSION_KEY, true);
            } else {
                $request->getQuery()->offsetset(self::FILTER_SESSION_KEY, true);
            }
        }
    }
}
