<?php
namespace PageBuilder\Model;

use Interop\Container\ContainerInterface;
use SynergyCommon\Entity\AbstractEntity;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Filter\Word\DashToCamelCase;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

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
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (substr($requestedName, 0, strlen($this->_configPrefix)) != $this->_configPrefix) {
            return false;
        }

        return true;
    }

    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return BaseModel
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var $authService \Zend\Authentication\AuthenticationService */
        $modelId = str_replace($this->_configPrefix, '', $requestedName);
        $idParts = explode('\\', $modelId);
        $filter  = new DashToCamelCase();

        if ($idParts[0] == 'join') {
            $entityId   = $idParts[1];
            $modelName  = __NAMESPACE__ . '\\' . $filter->filter($idParts[1]) . 'Model';
            $entityName = 'pagebuilder\entity\\' . $idParts[1];
        } else {
            $modelName = __NAMESPACE__ . '\\' . $filter->filter($modelId) . 'Model';
            /** @var AbstractEntity $entity */
            $entityName = 'pagebuilder\entity\\' . $modelId;
            $entityId   = $modelId;
        }

        $entity = $this->resolveEntityClassName($serviceLocator, $entityId, $entityName);

        /** @var BaseModel $model */
        $model = new $modelName();
        $model->setServiceLocator($serviceLocator);

        return $this->setDependency($serviceLocator, $model, $entity);
    }

    /**
     * @param ContainerInterface $serviceLocator
     * @param $id
     * @param $default
     * @return mixed
     */
    private function resolveEntityClassName(ContainerInterface $serviceLocator, $id, $default)
    {
        /** @var $sampleService \SynergyCommon\Service\BaseService */
        $reverseFilter   = new CamelCaseToDash();
        $entityId        = strtolower($reverseFilter->filter($id));
        $sampleService   = $serviceLocator->get('synergycommon\service\base');
        $entityClassName = $sampleService->getClassnameFromEntityKey($entityId);

        if ($entityClassName) {
            return new $entityClassName;
        }
        return $serviceLocator->get($default);
    }
}
