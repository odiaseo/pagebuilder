<?php
namespace PageBuilder\Session;

use Interop\Container\ContainerInterface;
use PageBuilder\Entity\Site;
use SynergyCommon\Exception\MissingArgumentException;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class LocalSiteFactory
 *
 * @package PageBuilder\Service
 */
class SessionStorageFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     *
     * @return mixed|Site
     * @throws MissingArgumentException
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $session = new Session();

        return $session;
    }
}
