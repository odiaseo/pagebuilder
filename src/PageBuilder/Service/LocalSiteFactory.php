<?php

namespace PageBuilder\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;
use PageBuilder\Entity\Site;
use PageBuilder\Model\SiteModel;
use SynergyCommon\Exception\MissingArgumentException;
use SynergyCommon\Model\AbstractModel;
use SynergyCommon\ModelTrait\LocaleAwareTrait;
use SynergyCommon\Util;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use Laminas\Session\SessionManager;

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
     * @return mixed|object|Site
     * @throws MissingArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var  $serviceLocator ServiceManager */
        $request = $serviceLocator->get('application')->getRequest();
        $event = $serviceLocator->get('application')->getMvcEvent();

        list($isConsole, $hostname) = Util::getDomainFromRequest($request, $event);

        try {
            //Important that this function is called here to initialize session
            $manager = $serviceLocator->get(SessionManager::class);
        } catch (\Exception $e) {
            $manager = null;
            //do nothing
        }

            if ($hostname) {
            /** @var SiteModel $model */
            $config = $serviceLocator->get('config');
            $globalDomain = Util::cleanDomain($config['pagebuilder']['global_domain']);
            $model = $serviceLocator->get('pagebuilder\model\site');

            if (!$site = $model->findSiteBy(['domain' => $hostname])) {
                $message = "Site is not registered";
                //$serviceLocator->get('logger')->warn($hostname . ' - domain was requested but not found');
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
            $site->setid(28);
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
}
