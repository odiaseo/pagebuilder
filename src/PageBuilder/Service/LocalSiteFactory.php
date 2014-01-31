<?php
namespace PageBuilder\Service;

use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class LocalSiteFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        /** @var  $serviceLocator \Zend\Servicemanager\ServiceManager */
        $containerKey = 'active-site';
        $request      = $serviceLocator->get('application')->getRequest();

        if ($request instanceof Request) {
            /** @var $event \Zend\Mvc\MvcEvent */
            $event = $serviceLocator->get('application')->getMvcEvent();
            /** @var $rm \Zend\Mvc\Router\RouteMatch */
            $rm   = $event->getRouteMatch();
            $host = $rm->getParam('host');
        } else {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $host = $request->getServer('HTTP_HOST');
        }

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

        return $site;

    }
}