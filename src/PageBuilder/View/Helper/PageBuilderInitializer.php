<?php
namespace PageBuilder\View\Helper;

use Interop\Container\ContainerInterface;
use PageBuilder\FormatterInterface;
use PageBuilder\View\Helper\Config\PageBuilderConfig;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Class WidgetInitializers
 * @package PageBuilder\Service
 */
class PageBuilderInitializer implements InitializerInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param object $helper
     * @return bool
     */
    public function __invoke(ContainerInterface $serviceManager, $helper)
    {
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        if ($helper instanceof PageBuilder) {
            $config = $serviceManager->get('config');

            $formatters = array();
            foreach ($config['pagebuilder']['output_formatters'] as $format) {
                if (is_string($format)) {
                    $formatters[] = $serviceManager->get($format);
                } elseif ($format instanceof FormatterInterface) {
                    $formatters[] = $format;
                } elseif (is_callable($format)) {
                    $formatters[] = $format;
                } else {
                    continue;
                }
            }

            /** @var $theme \PageBuilder\Entity\Theme */

            $theme         = $serviceManager->get('active_theme');
            $builderConfig = $config['pagebuilder'];
            if ($theme) {
                $builderConfig['bootstrap_version'] = $theme->getBootstrapVersion();
            }

            if (is_string($builderConfig['replacements'])) {
                $builderConfig['replacements'] = $serviceManager->get($builderConfig['replacements']);
            }

            $options = new PageBuilderConfig($builderConfig);
            $options->setOutputFormatters($formatters);

            $helper->setOptions($options);
            return true;
        }

        return false;
    }
}
