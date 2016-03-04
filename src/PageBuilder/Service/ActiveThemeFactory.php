<?php
namespace PageBuilder\Service;

use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class LocalSiteFactory
 *
 * @package PageBuilder\Service
 */
class ActiveThemeFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $site  = $serviceLocator->get('active\site');
        $theme = $serviceLocator->get('pagebuilder\model\theme')->getActiveTheme($site->getId());

        return $theme;
    }
}
