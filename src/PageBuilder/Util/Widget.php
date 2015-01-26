<?php

namespace PageBuilder\Util;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Class Widget
 *
 * @package PageBuilder\Util
 */
class Widget implements ServiceManagerAwareInterface
{
    const CACHE_LOCATION = 'data/cache/config/widgets.php';

    public static $registry = array();

    /** @var array */
    protected $widgetList;

    /** @var \Zend\ServiceManager\ServiceManager */
    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Gets lists of available widgets
     *
     * @return array
     */
    public function getWidgetList()
    {
        if ($this->isCacheEnabled() and file_exists(self::CACHE_LOCATION)) {
            return include self::CACHE_LOCATION;
        } elseif (!$this->widgetList) {
            $finalList    = array();
            $config       = $this->serviceManager->get('config');
            $dirLocations = (array)$config['pagebuilder']['widgets']['paths'];

            foreach ($dirLocations as $namespace => $dirLocation) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($dirLocation, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );

                /** @var $splFileInfo \SplFileInfo */
                foreach ($iterator as $splFileInfo) {
                    $ext = substr(basename($splFileInfo->getFilename()), -4);
                    if ($splFileInfo->isFile() and $ext == '.php') {
                        $widgetId  = substr(basename($splFileInfo->getFilename()), 0, -4);
                        $className = substr(
                            $namespace . str_replace(
                                '/', "\\", str_replace($dirLocation, '', $splFileInfo->getPathname())
                            ),
                            0, -4
                        );

                        $reflection = new \ReflectionClass($className);

                        if ($reflection->isInstantiable()
                            and $reflection->implementsInterface('PageBuilder\WidgetInterface')
                        ) {
                            $attributes = $reflection->getDefaultProperties();
                            $id         = !empty($attributes['id']) ? preg_replace(
                                '/[^a-z]/i', '',
                                $attributes['id']
                            )
                                : $widgetId;
                            $id         = strtolower($id);
                            $category   = basename(dirname($splFileInfo->getPathname()));

                            $data = array(
                                'id'          => $id,
                                'class'       => $className,
                                'category'    => ($category == 'Widget') ? 'General' : $category,
                                'title'       => $attributes['name'] ?: $widgetId,
                                'description' => $attributes['description'] ?: 'No description found',
                                'options'     => $attributes['options']
                            );

                            $path                = array($id => $data);
                            self::$registry[$id] = $data;

                        } else {
                            continue;
                        }

                    } else {
                        $dirName = $splFileInfo->getFilename();
                        $path    = array($dirName => array());
                    }

                    for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                        $dirName = $iterator->getSubIterator($depth)->current()->getFilename();
                        $path    = array($dirName => $path);
                    }

                    uasort(
                        $path,
                        function ($a, $b) {
                            return strcmp($a['title'], $b['title']);
                        }
                    );

                    $finalList = array_merge_recursive($finalList, $path);
                }
            }
            $this->widgetList = $finalList;
        }

        if ($this->isCacheEnabled()) {
            $data = '<?php return ' . var_export($this->widgetList, true) . ' ;';
            file_put_contents(self::CACHE_LOCATION, $data);
        }

        return $this->widgetList;
    }

    /**
     *  Checks if a widget exists
     *
     * @param $name
     *
     * @return bool
     */
    public function widgetExist($name)
    {
        $name = strtolower($name);

        if (empty(self::$registry)) {
            $this->getWidgetList();
        }

        if (isset(self::$registry[$name])) {
            return self::$registry[$name];
        } else {
            return false;
        }
    }

    public static function getRegistry()
    {
        return self::$registry;
    }

    /**
     * @return boolean
     */
    private function isCacheEnabled()
    {
        $status = $this->serviceManager->get('cache\status');

        return $status->enabled;
    }
}
