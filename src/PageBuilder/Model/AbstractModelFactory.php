<?php
namespace PageBuilder\Model;


use Doctrine\ORM\EntityManager;
use SynergyCommon\Doctrine\CachedEntityManager;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractModelFactory implements AbstractFactoryInterface {

	protected $_configPrefix;

	public function __construct() {
		$this->_configPrefix = strtolower( __NAMESPACE__ ) . '\\';
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
	public function canCreateServiceWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName ) {
		if ( substr( $requestedName, 0, strlen( $this->_configPrefix ) ) != $this->_configPrefix ) {
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
	public function createServiceWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName ) {
		/** @var $authService \Zend\Authentication\AuthenticationService */
		$modelId = str_replace( $this->_configPrefix, '', $requestedName );
		$idParts = explode( '\\', $modelId );
		$config  = $serviceLocator->get( 'config' );

		if ( $idParts[0] == 'join' ) {
			$modelName = __NAMESPACE__ . '\\' . ucfirst( $idParts[1] ) . 'Model';
			$entity    = $serviceLocator->get( 'pagebuilder\entity\\' . $idParts[1] );
		} else {
			$modelName = __NAMESPACE__ . '\\' . ucfirst( $modelId ) . 'Model';
			$entity    = $serviceLocator->get( 'pagebuilder\entity\\' . $modelId );
		}
		if ( $serviceLocator->has( 'zfcuser_auth_service' ) ) {
			$authService = $serviceLocator->get( 'zfcuser_auth_service' );
			$identity    = $authService->hasIdentity();
		} else {
			$identity = false;
		}
		if ( $identity ) {
			$enabled = false;
		} elseif ( isset( $config['enable_result_cache'] ) ) {
			$enabled = $config['enable_result_cache'];
		} else {
			$enabled = false;
		}
		/** @var $model \PageBuilder\Model\BaseModel */
		/** @var EntityManager $entityManager */
		$model         = new $modelName();
		$entityManager = $serviceLocator->get( 'doctrine.entitymanager.' . $model->getOrm() );
		$cachedManager = new CachedEntityManager( $entityManager, $enabled );

		$model->setEnableResultCache( $enabled );
		$model->setEntityInstance( $entity );
		$model->setEntity( get_class( $entity ) );
		$model->setLogger( $serviceLocator->get( 'logger' ) );
		$model->setEntityManager( $cachedManager );

		return $model;
	}
}
