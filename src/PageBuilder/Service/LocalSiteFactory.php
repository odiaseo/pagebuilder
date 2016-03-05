<?php
namespace PageBuilder\Service;

use PageBuilder\Entity\Site;
use PageBuilder\Model\SiteModel;
use SynergyCommon\Exception\MissingArgumentException;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\ModelTrait\LocaleAwareTrait;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\Console\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

/**
 * Class LocalSiteFactory
 *
 * @package PageBuilder\Service
 */
class LocalSiteFactory implements FactoryInterface
{

    const CLIENT_DOMAIN_KEY = 'client_domain';

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed|Site
     * @throws MissingArgumentException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var  $serviceLocator \Zend\Servicemanager\ServiceManager */
        $host      = null;
        $isConsole = false;
        $request   = $serviceLocator->get('application')->getRequest();
        $event     = $serviceLocator->get('application')->getMvcEvent();
        //IMportant to run this first so that the session is initialised
        $manager = $this->initialiseSessionManager($serviceLocator, $request);

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
                    header('HTTP/1.1 302 Domain not found');
                    header('Location: ' . $destination);
                } else {
                    header('HTTP/1.1 403 Application Error');
                    echo $message;
                }
                exit();
            }
        } elseif ($isConsole) {
            $site = new Site();
        } else {
            throw new MissingArgumentException('Host not found');
        }

        $container = new Container(LocaleAwareTrait::getNamespace(), $manager);
        if (!$i18nLocale = $site->getI18nLocale()) {
            $i18nLocale = $site->getLocale();
        }
        $container->offsetSet(AbstractModel::SESSION_LOCALE_KEY, $i18nLocale);
        $container->offsetSet(AbstractModel::SESSION_SITE_KEY, $host);

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
        $hackReplacements = [
            //'www.ww.',
            //'www.w.',
            // 'ww.w.',
            // 'ww.',
            // 'doc1000.',
            'admin.'
        ];

        $domain = str_replace(array('http://', 'https://', 'www.'), '', $domain);

        return str_replace($hackReplacements, '', $domain);
    }

    /**
     * @param $serviceLocator
     */
    private function initialiseSessionManager($serviceLocator, $request)
    {
        $manager = null;
        if ($request instanceof \Zend\Http\PhpEnvironment\Request) {
            if ($serviceLocator->has('session_manager')) {
                $manager = $serviceLocator->get('session_manager');
                /** @var AuthenticationService $authService */
                $authService = $serviceLocator->get('zfcuser_auth_service');
                $session     = new Session(null, null, $manager);
                $authService->getStorage()->setStorage($session);
            }
        }

        return $manager;
    }
}
