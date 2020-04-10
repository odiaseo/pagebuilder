<?php

namespace PageBuilder\Util;

use Interop\Container\ContainerInterface;

/**
 * Class Widget
 *
 * @package PageBuilder\Util
 */
class Widget
{
    const CACHE_LOCATION = 'data/cache/config/widgets.php';

    public static $registry = [];

    /** @var array */
    protected $widgetList;

    /** @var \Laminas\ServiceManager\ServiceManager */
    protected $serviceManager;

    /** @var array */
    private $dataStore = [];

    /**
     * @param ContainerInterface $serviceManager
     */
    public function setServiceManager(ContainerInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return array
     */
    public function getWidgetList()
    {
        return isset($this->dataStore[1]) ? $this->dataStore[1] : [];
    }

    /**
     * Gets lists of available widgets
     *
     * @return array
     */
    public function init()
    {
        if ($this->isCacheEnabled() and file_exists(self::CACHE_LOCATION)) {
            $store = include self::CACHE_LOCATION;
        } else {
            $store        = [];
            $finalList    = [];
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

                            $data = [
                                'id'          => $id,
                                'class'       => $className,
                                'category'    => ($category == 'Widget') ? 'General' : $category,
                                'title'       => $attributes['name'] ?: $widgetId,
                                'description' => $attributes['description'] ?: 'No description found',
                                'options'     => $attributes['options'],
                            ];

                            $path          = [$id => $data];
                            $store[0][$id] = $data;
                        } else {
                            continue;
                        }
                    } else {
                        $dirName = $splFileInfo->getFilename();
                        $path    = [$dirName => []];
                    }

                    for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                        $dirName = $iterator->getSubIterator($depth)->current()->getFilename();
                        $path    = [$dirName => $path];
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

            $store[1] = $finalList;

            if ($this->isCacheEnabled()) {
                $data = '<?php return ' . var_export($store, true) . ' ;';
                file_put_contents(self::CACHE_LOCATION, $data);
            }
        }

        $this->dataStore = $store;
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

        return isset($this->dataStore[0][$name]) ? $this->dataStore[0][$name] : false;
    }

    public function getRegistry()
    {
        return isset($this->dataStore[0]) ? $this->dataStore[0] : [];
    }

    /**
     * @return boolean
     */
    private function isCacheEnabled()
    {
        $status = $this->serviceManager->get('synergy\cache\status');

        return $status->enabled;
    }
}
