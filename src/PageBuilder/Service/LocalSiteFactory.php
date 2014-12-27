<?php
namespace PageBuilder\Service;

use PageBuilder\Model\SiteModel;
use SynergyCommon\Exception\MissingArgumentException;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocalSiteFactory implements FactoryInterface {

	const CLIENT_DOMAIN_KEY = 'client_domain';

	public function createService( ServiceLocatorInterface $serviceLocator ) {

		$host = null;
		/** @var  $serviceLocator \Zend\Servicemanager\ServiceManager */
		$request = $serviceLocator->get( 'application' )->getRequest();

		if ( $request instanceof Request ) {
			/** @var $event \Zend\Mvc\MvcEvent */
			$event = $serviceLocator->get( 'application' )->getMvcEvent();
			/** @var $rm \Zend\Mvc\Router\RouteMatch */
			if ( $rm = $event->getRouteMatch() ) {
				$host = $rm->getParam( 'host', $rm->getParam( self::CLIENT_DOMAIN_KEY, null ) );
			}
		} else {
			/** @var $request \Zend\Http\PhpEnvironment\Request */
			$host = $request->getServer( 'HTTP_HOST', $request->getQuery( self::CLIENT_DOMAIN_KEY, null ) );
		}

		if ( $host ) {
			list( $host, ) = explode( ':', $host );
			$hostname = str_replace( array( 'http://', 'https://', 'www.' ), '', $host );
			/** @var SiteModel $model */
			$model = $serviceLocator->get( 'pagebuilder\model\site' );
			if ( ! $site = $model->findSiteBy( array( 'domain' => $hostname ) ) ) {
				header( 'HTTP/1.1 403 Application Error' );
				echo "Site is not registered";
				exit;
			}
		} else {
			throw new MissingArgumentException( 'Host not found' );
		}

		return $site;

	}
}
