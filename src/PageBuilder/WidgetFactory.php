<?php
namespace PageBuilder;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WidgetFactory implements AbstractFactoryInterface
{
    const WIDGET_SUFFIX = 'widget';
    protected $_config = array();
    public static $registry = array();
    protected $_sm;

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {

        if (substr($requestedName, -6) == self::WIDGET_SUFFIX) {
            $widgetId = str_replace(self::WIDGET_SUFFIX, '', $requestedName);

            return $this->widgetExist($widgetId, $serviceLocator);
        }

        return false;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $this->_sm = $serviceLocator;
        $widgetId  = str_replace(self::WIDGET_SUFFIX, '', $name);

        if ($data = $this->widgetExist($widgetId, $serviceLocator)) {
            /** @var $widget \PageBuilder\BaseWidget */
            $widget = new $data['class']();
            $widget->setId($widgetId);

            return $widget;
        }

        return false;

    }

    /**
     *  Checks if a widget exists
     *
     * @param $name
     * @param $sm
     *
     * @return bool
     */
    public function widgetExist($name, $sm)
    {
        /** @var $sm \Zend\ServiceManager\ServiceLocatorInterface */
        $name = strtolower($name);

        if (empty(self::$registry)) {
            $config        = $sm->get('config');
            $this->_config = $config['widgets'];
            self::getWidgetList($config['widgets']['directory_location']);
        }

        if (isset(self::$registry[$name])) {
            return self::$registry[$name];
        } else {
            return false;
        }
    }

    /**
     * Gets widget registry
     *
     * @param array $dirLocations
     *
     * @return array
     */
    public static function getWidgetList(array $dirLocations)
    {
        $r = array();
        foreach ($dirLocations as $dirLocation) {
            $iterator  = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dirLocation, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            $namespace = __NAMESPACE__ . '\Widget';

            /** @var $splFileInfo \SplFileInfo */
            foreach ($iterator as $splFileInfo) {

                if ($splFileInfo->isFile()) {
                    $widgetId   = substr(basename($splFileInfo->getFilename()), 0, -4);
                    $className  = substr(
                        $namespace . str_replace('/', "\\", str_replace($dirLocation, '', $splFileInfo->getPathname())),
                        0, -4
                    );
                    $reflection = new \ReflectionClass($className);

                    if ($reflection->implementsInterface(__NAMESPACE__ . '\WidgetInterface')) {
                        $attributes = $reflection->getDefaultProperties();
                        $id         = !empty($attributes['id']) ? preg_replace('/[^a-z]/i', '', $attributes['id'])
                            : $widgetId;
                        $id         = strtolower($id);
                        $category   = basename(dirname($splFileInfo->getPathname()));

                        $data = array(
                            'id'          => $id,
                            'class'       => $className,
                            'category'    => ($category == 'Widget') ? 'General' : $category,
                            'title'       => $attributes['name'] ? : $widgetId,
                            'description' => $attributes['description'] ? : 'No description found',
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
                    $path, function ($a, $b) {
                        return strcmp($a['title'], $b['title']);
                    }
                );

                $r = array_merge_recursive($r, $path);
            }
        }

        return $r;
    }
}