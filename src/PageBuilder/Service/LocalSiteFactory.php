<?php
namespace PageBuilder\Service;

use PageBuilder\Entity\Site;
use PageBuilder\Model\SiteModel;
use SynergyCommon\Exception\MissingArgumentException;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class LocalSiteFactory implements FactoryInterface
{

    const CLIENT_DOMAIN_KEY  = 'client_domain';
    const SESSION_LOCALE_KEY = 'active_locale';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $host      = null;
        $isConsole = false;
        /** @var  $serviceLocator \Zend\Servicemanager\ServiceManager */
        $request = $serviceLocator->get('application')->getRequest();
        $event   = $serviceLocator->get('application')->getMvcEvent();

        if ($request instanceof Request) {
            /** @var $event \Zend\Mvc\MvcEvent */
            $isConsole = true;
            /** @var $rm \Zend\Mvc\Router\RouteMatch */
            if ($rm = $event->getRouteMatch()) {
                $host = $rm->getParam('host', $rm->getParam(self::CLIENT_DOMAIN_KEY, null));
            }
        } else {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $host = $request->getServer('HTTP_HOST', $request->getQuery(self::CLIENT_DOMAIN_KEY, null));
        }

        if ($host) {
            /** @var SiteModel $model */
            $config = $serviceLocator->get('config');
            list($host,) = explode(':', $host);
            $globalDomain = $this->cleanDomain($config['pagebuilder']['global_domain']);
            $hostname     = $this->cleanDomain($host);
            $model        = $serviceLocator->get('pagebuilder\model\site');

            if (!$site = $model->findSiteBy(array('domain' => $hostname))) {
                $message = "Site is not registered";
                $serviceLocator->get('logger')->warn($host . ' - domain was requested but not found');
                if (!$isConsole and $host != $globalDomain) {
                    $destination = sprintf('http://www.%s?nfh=%s', $globalDomain, $host);
                    header('HTTP/1.1 401 Domain not found');
                    header('Location: ' . $destination);
                } else {
                    header('HTTP/1.1 403 Application Error');
                    echo $message;
                }
                exit();
            }
        } elseif ($isConsole) {
            $site = new Site();
            $site->setId(1);
        } else {
            throw new MissingArgumentException('Host not found');
        }

        $container = new Container();
        $container->offsetSet(self::SESSION_LOCALE_KEY, $site->getLocale());
        //Set locale
        \setlocale(LC_ALL, $site->getLocale());

        //Set timezone
        if ($timezone = $site->getDefaultTimezone()) {
            \date_default_timezone_set($timezone);
        }

        return $site;
    }

    /**
     * @param $domain
     *
     * @return mixed
     */
    protected function cleanDomain($domain)
    {
        return str_replace(array('http://', 'https://', 'www.'), '', $domain);
    }
}
