<?php
namespace PageBuilder\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class LocalSiteFactory
 *
 * @package PageBuilder\Service
 */
class ActiveThemeFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $site  = $serviceLocator->get('active\site');
        $theme = $serviceLocator->get('pagebuilder\model\theme')->getActiveTheme($site->getId());

        return $theme;
    }
}
