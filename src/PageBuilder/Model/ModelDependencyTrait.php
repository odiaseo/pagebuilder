<?php
namespace PageBuilder\Model;

use Doctrine\ORM\EntityManager;
use PageBuilder\LocaleAwareInterface;
use SynergyCommon\Doctrine\CachedEntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

trait ModelDependencyTrait {

	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 * @param BaseModel               $model
	 * @param                         $entity
	 *
	 * @return BaseModel
	 */
	protected function setDependency( ServiceLocatorInterface $serviceLocator, BaseModel $model, $entity ) {

		/** @var EntityManager $entityManager */
		$cacheStatus   = $serviceLocator->get( 'synergy\cache\status' );
		$entityManager = $serviceLocator->get( 'doctrine.entitymanager.' . $model->getOrm() );
		$cachedManager = new CachedEntityManager( $entityManager, $cacheStatus->enabled );

		if ( $serviceLocator->has( 'zfcuser_auth_service' ) ) {
			$authService = $serviceLocator->get( 'zfcuser_auth_service' );
			$identity    = $authService->hasIdentity();
			$model->setIdentity( $authService->getIdentity() );
		} else {
			$identity = false;
	}

		if ( is_string( $entity ) ) {
			$model->setEntity( $entity );
		} else {
			$model->setEntityInstance( $entity );
			$model->setEntity( get_class( $entity ) );
		}

		if ( $model instanceof LocaleAwareInterface ) {
			$site = $serviceLocator->get( 'active_site' );
			if ( $locale = $site->getLocale() ) {
				$model->setLocale( $locale );
			}
		}

		$enabled = ( ! $identity && $cacheStatus->enabled );
		$model->setEnableResultCache( $enabled );
		$model->setLogger( $serviceLocator->get( 'logger' ) );
		$model->setEntityManager( $cachedManager );

		return $model;
	}
}
