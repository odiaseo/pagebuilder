<?php
namespace PageBuilderTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Application;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

chdir(dirname(__DIR__));

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

$basePath = realpath('./') . DIRECTORY_SEPARATOR;

set_include_path(
    implode(
        PATH_SEPARATOR,
        array($basePath,
              realpath($basePath . '/src'),
              realpath($basePath . '/vendor'),
              realpath($basePath . '/tests'),
              get_include_path(),
        )
    )
);

$GLOBALS['basePath'] = $basePath;

class Bootstrap
{
    public static $application;
    public static $serviceManager;

    public static function init()
    {
        global $basePath;
        $zf2Path = __DIR__ . '/../vendor/zendframework/zendframework/library';


        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            $loader = include __DIR__ . '/../vendor/autoload.php';
        }

        // Support for ZF2_PATH environment variable or git submodule

        if (isset($loader)) {
            $loader->add('Zend', $zf2Path . '/Zend');

        } else {
            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
            AutoloaderFactory::factory(
                array(
                     'Zend\Loader\StandardAutoloader' => array(
                         'autoregister_zf' => true
                     )
                )
            );
        }


        $classList = include __DIR__ . '/../autoload_classmap.php';


        spl_autoload_register(
            function ($class) use ($classList, $basePath) {
                if (isset($classList[$class])) {
                    @include $classList[$class];
                } else {
                    $filename = str_replace('\\\\', '/', $class) . '.php';
                    @include($filename);
                }
            }
        );

        $zf2ModulePaths = array(dirname(dirname(__DIR__)));
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('src')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        $zf2ModulePaths[] = './';

        $config = array(
            'module_listener_options' => array(
                'module_paths'      => $zf2ModulePaths,

                'config_glob_paths' => array(
                    'config/autoload/{,*.}{global,local}.php',
                ),
            ),

            'modules'                 => array(
                'PageBuilder',
                'DoctrineModule',
                'DoctrineORMModule',
                'SynergyDataGrid',
            )
        );


        include __DIR__ . '/../init_autoloader.php';
        self::$application = Application::init($config);


        $serviceManager = new ServiceManager(new ServiceManagerConfig(include __DIR__ . '/../tests/testconfig.php'));
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;

    }

    /**
     *
     * @param string $path
     *
     * @return boolean|string false if the path cannot be found
     */
    protected static function findParentPath($path)
    {
        $dir    = __DIR__;
        $srcDir = realpath($dir . '/../');

        return $srcDir . '/' . $path;
    }

    public static function getServiceManager()
    {
        return self::$serviceManager;
    }

    public static function getApplication()
    {
        return self::$application;
    }
}

Bootstrap::init();