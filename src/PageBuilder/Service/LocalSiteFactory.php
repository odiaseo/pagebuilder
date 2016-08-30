<?php
namespace PageBuilder\Service;

use Interop\Container\ContainerInterface;
use PageBuilder\Entity\Site;
use PageBuilder\Model\SiteModel;
use SynergyCommon\Exception\MissingArgumentException;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\ModelTrait\LocaleAwareTrait;
use SynergyCommon\Util;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
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
     * @param ContainerInterface $serviceLocator
     * @param string $requestedName
     * @param array|null $options
     * @return mixed|Site
     * @throws MissingArgumentException
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var  $serviceLocator \Zend\Servicemanager\ServiceManager */
        $request = $serviceLocator->get('application')->getRequest();
        $event   = $serviceLocator->get('application')->getMvcEvent();

        //Important to run this first so that the session is initialised
        $manager = $this->initialiseSessionManager($serviceLocator, $request);

        list($isConsole, $hostname) = Util::getDomainFromRequest($request, $event);

        if ($hostname) {
            /** @var SiteModel $model */
            $config       = $serviceLocator->get('config');
            $globalDomain = Util::cleanDomain($config['pagebuilder']['global_domain']);
            $model        = $serviceLocator->get('pagebuilder\model\site');

            if (!$site = $model->findSiteBy(array('domain' => $hostname))) {
                $message = "Site is not registered";
                $serviceLocator->get('logger')->warn($hostname . ' - domain was requested but not found');
                if (!$isConsole and $hostname != $globalDomain) {
                    $destination = sprintf('http://www.%s?nfh=%s', $globalDomain, $hostname);
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
        $container->offsetSet(AbstractModel::SESSION_ALLOWED_SITE_KEY, $site->getAllowedSites());

        //Set locale
        \setlocale(LC_ALL, $site->getLocale());

        //Set timezone
        if ($timezone = $site->getDefaultTimezone()) {
            \date_default_timezone_set($timezone);
        }

        return $site;
    }

    /**
     * @param $serviceLocator
     * @param $request
     * @return null
     */
    private function initialiseSessionManager($serviceLocator, $request)
    {
        $manager = null;
        if ($request instanceof \Zend\Http\PhpEnvironment\Request) {
            if ($serviceLocator->has('session_manager')) {
                /** @var AuthenticationService $authService */
                $manager = $serviceLocator->get('session_manager');
                $serviceLocator->get(AuthenticationService::class);
            }
        }

        return $manager;
    }
}
