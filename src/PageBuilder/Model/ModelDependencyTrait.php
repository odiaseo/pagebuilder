<?php
namespace PageBuilder\Model;

use Doctrine\ORM\EntityManager;
use PageBuilder\LocaleAwareInterface;
use PageBuilder\Service\LocalSiteFactory;
use SynergyCommon\Doctrine\CachedEntityManager;
use SynergyCommon\Model\AbstractModel;
use Zend\ServiceManager\ServiceLocatorInterface;

trait ModelDependencyTrait
{

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param AbstractModel           $model
     * @param                         $entity
     *
     * @return BaseModel
     */
    protected function setDependency(ServiceLocatorInterface $serviceLocator, AbstractModel $model, $entity)
    {

        /** @var EntityManager $entityManager */
        $cacheStatus   = $serviceLocator->get('synergy\cache\status');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.' . $model->getOrm());
        $cachedManager = new CachedEntityManager($entityManager, $cacheStatus->enabled);

        if ($serviceLocator->has('zfcuser_auth_service')) {
            $authService = $serviceLocator->get('zfcuser_auth_service');
            $identity    = $authService->hasIdentity();
            $model->setIdentity($authService->getIdentity());
        } else {
            $identity = false;
        }

        if (is_string($entity)) {
            $model->setEntity($entity);
        } elseif ($model instanceof BaseModel) {
            $model->setEntityInstance($entity);
            $model->setEntity(get_class($entity));
        }

        if ($model instanceof LocaleAwareInterface) {
            $site = $serviceLocator->get('active\site');
            if ($locale = $site->getLocale()) {
                $model->setLocale($locale);

            }
        }

        $enabled = (false == $identity and $cacheStatus->enabled);
        $model->setEnableResultCache($enabled);
        $model->setLogger($serviceLocator->get('logger'));
        $model->setEntityManager($cachedManager);

        return $model;
    }
}
