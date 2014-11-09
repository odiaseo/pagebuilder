<?php
namespace PageBuilder\DataProvider;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GridDefault
 *
 * @package PageBuilder\DataProvider
 */
class GridDefault implements FactoryInterface {
	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 *
	 * @return array|mixed
	 */
	public function createService( ServiceLocatorInterface $serviceLocator ) {
		/** @var \PageBuilder\Entity\Site $site */
		$site = $serviceLocator->get( 'active_site' );
		$data = array(
			'global'   => array(
				'site'      => $site->getId(),
				'siteId'    => $site->getId(),
				'createdAt' => date( 'Y-m-d H:i:s' ),
				'timezone'  => 'UTC',
			),
			'specific' => array()
		);

		return $data;
	}
}
