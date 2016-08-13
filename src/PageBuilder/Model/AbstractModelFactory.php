<?php
namespace PageBuilder\Model;

use SynergyCommon\Entity\AbstractEntity;
use Zend\Filter\Word\DashToCamelCase;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractModelFactory
 *
 * @package PageBuilder\Model
 */
class AbstractModelFactory implements AbstractFactoryInterface
{
    use ModelDependencyTrait;
    protected $_configPrefix;

    public function __construct()
    {
        $this->_configPrefix = strtolower(__NAMESPACE__) . '\\';
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (substr($requestedName, 0, strlen($this->_configPrefix)) != $this->_configPrefix) {
            return false;
        }

        return true;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        /** @var $authService \Zend\Authentication\AuthenticationService */
        $modelId = str_replace($this->_configPrefix, '', $requestedName);
        $idParts = explode('\\', $modelId);
        $filter = new DashToCamelCase();

        if ($idParts[0] == 'join') {
            $modelName = __NAMESPACE__ . '\\' . $filter->filter($idParts[1]) . 'Model';
            $entity    = $serviceLocator->get('pagebuilder\entity\\' . $idParts[1]);
        } else {
            $modelName = __NAMESPACE__ . '\\' . $filter->filter($modelId) . 'Model';
            /** @var AbstractEntity $entity */
            $entity = $serviceLocator->get('pagebuilder\entity\\' . $modelId);
        }
        /** @var BaseModel $model */
        $model = new $modelName();
        $model->setServiceLocator($serviceLocator);

        return $this->setDependency($serviceLocator, $model, $entity);
    }
}
