<?php

namespace PageBuilderTest;

/**
 * Class Module
 * @package PageBuilderTest
 */
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/test.global.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
