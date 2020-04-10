<?php
namespace PageBuilder\Model;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use PageBuilder\Entity\Site;
use PageBuilder\LocaleAwareInterface;
use SynergyCommon\CacheAwareInterface;
use SynergyCommon\Doctrine\CachedEntityManager;
use SynergyCommon\Model\AbstractModel;
use Laminas\Authentication\AuthenticationService;

trait ModelDependencyTrait
{

    /**
     * @param ContainerInterface $serviceLocator
     * @param AbstractModel $model
     * @param                         $entity
     *
     * @return BaseModel
     */
    protected function setDependency(ContainerInterface $serviceLocator, AbstractModel $model, $entity)
    {

        /** @var EntityManager $entityManager */
        $cacheStatus   = $serviceLocator->get('synergy\cache\status');
        $entityManager = $serviceLocator->get('doctrine.entitymanager.' . $model->getOrm());
        $config        = $serviceLocator->get('config');
        $site          = null;
        $filterName    = 'super_sites';

        if ($serviceLocator->has(AuthenticationService::class)) {
            /** @var AuthenticationService $authService */
            $authService = $serviceLocator->get(AuthenticationService::class);
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
        /** @var Site $site */
        if (!$model instanceof SiteModel) {
            $site = $serviceLocator->get('active\site');

            if ($site and in_array($site->getId(), $config['super_sites']) and
                $entityManager->getFilters()->has($filterName) and
                $entityManager->getFilters()->isEnabled($filterName)
            ) {
                $entityManager->getFilters()->disable($filterName);
            }
        }

        if ($site and $model instanceof LocaleAwareInterface) {
            if ($locale = $site->getLocale()) {
                $model->setLocale($locale);
            }
        }

        if ($model instanceof CacheAwareInterface) {
            $model->setCache('system\cache');
        }
        $enabled       = (!$identity and $cacheStatus->enabled);
        $cachedManager = new CachedEntityManager($entityManager, $enabled);

        $model->setEnableResultCache($enabled);
        $model->setLogger($serviceLocator->get('logger'));
        $model->setEntityManager($cachedManager);
        $model->setServiceLocator($serviceLocator);

        return $model;
    }
}
