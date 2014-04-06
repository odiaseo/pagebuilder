<?php
namespace PageBuilder\Service;

use PageBuilder\Entity\Site;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class LocalSiteFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $host = null;

        /** @var  $serviceLocator \Zend\Servicemanager\ServiceManager */
        $containerKey = 'active-site';
        $request      = $serviceLocator->get('application')->getRequest();

        if ($request instanceof Request) {
            /** @var $event \Zend\Mvc\MvcEvent */
            $event = $serviceLocator->get('application')->getMvcEvent();
            /** @var $rm \Zend\Mvc\Router\RouteMatch */
            if ($rm = $event->getRouteMatch()) {
                $host = $rm->getParam('host');
            }
        } else {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $host = $request->getServer('HTTP_HOST');
        }
        if ($host) {
            $hostname   = str_replace(array('http://', 'https://', 'www.'), '', $host);
            $sessionKey = preg_replace('/[^\p{L}\p{N}]+/ui', '', "host{$hostname}");

            /** @var $container \ArrayObject */
            $container = new Container($sessionKey);

            if ($container->offsetExists($containerKey)) {
                $em   = $serviceLocator->get('doctrine.entitymanager.orm_default');
                $site = $container->offsetGet($containerKey);
                $site = $em->merge($site);
            } elseif (!$site = $serviceLocator->get('pagebuilder\model\site')->findOneByDomain($hostname)) {
                header
                (
                    'HTTP/1.1 403 Application Error'
                );
                echo "Site is not registered";
                exit;
            } else {
                $container->offsetSet($containerKey, $site);
            }
        } else {
            $site = new Site();
        }

        return $site;

    }
}