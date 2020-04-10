<?php
namespace PageBuilder\DataProvider;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class GridDefault
 *
 * @package PageBuilder\DataProvider
 */
class GridDefault implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     *
     * @return array
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var \PageBuilder\Entity\Site $site */
        $site = $serviceLocator->get('active\site');
        $data = [
            'global'   => [
                'site'      => $site->getId(),
                'siteId'    => $site->getId(),
                'createdAt' => date('Y-m-d H:i:s'),
                'timezone'  => 'UTC',
            ],
            'specific' => [],
        ];

        return $data;
    }
}
