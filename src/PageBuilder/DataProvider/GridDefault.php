<?php
namespace PageBuilder\DataProvider;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GridDefault
 *
 * @package PageBuilder\DataProvider
 */
class GridDefault implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return array|mixed
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var \PageBuilder\Entity\Site $site */
        $site = $serviceLocator->get('active\site');
        $data = array(
            'global'   => array(
                'site'      => $site->getId(),
                'siteId'    => $site->getId(),
                'createdAt' => date('Y-m-d H:i:s'),
                'timezone'  => 'UTC',
            ),
            'specific' => array()
        );

        return $data;
    }
}
