<?php

namespace PageBuilder\Util;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Widget
	implements ServiceManagerAwareInterface {

	public static $registry = array();

	/** @var array */
	protected $_widgetList;

	/** @var \Zend\ServiceManager\ServiceManager */
	protected $_serviceManager;

	public function setServiceManager( ServiceManager $serviceManager ) {
		$this->_serviceManager = $serviceManager;
	}

	/**
	 * Gets lists of available widgets
	 *
	 * @return array
	 */
	public function getWidgetList() {
		if ( ! $this->_widgetList ) {
			$r            = array();
			$config       = $this->_serviceManager->get( 'config' );
			$dirLocations = (array) $config['pagebuilder']['widgets']['paths'];


			foreach ( $dirLocations as $namespace => $dirLocation ) {
				$iterator = new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator( $dirLocation, \FilesystemIterator::SKIP_DOTS ),
					\RecursiveIteratorIterator::CHILD_FIRST
				);

				/** @var $splFileInfo \SplFileInfo */
				foreach ( $iterator as $splFileInfo ) {
					$ext = substr( basename( $splFileInfo->getFilename() ), - 4 );;
					if ( $splFileInfo->isFile() && $ext == '.php' ) {
						$widgetId  = substr( basename( $splFileInfo->getFilename() ), 0, - 4 );
						$className = substr(
							$namespace . str_replace(
								'/', "\\", str_replace( $dirLocation, '', $splFileInfo->getPathname() )
							),
							0, - 4
						);


						$reflection = new \ReflectionClass( $className );


						if ( $reflection->isInstantiable()
						     && $reflection->implementsInterface( 'PageBuilder\WidgetInterface' )
						) {
							$attributes = $reflection->getDefaultProperties();
							$id         = ! empty( $attributes['id'] ) ? preg_replace( '/[^a-z]/i', '',
								$attributes['id'] )
								: $widgetId;
							$id         = strtolower( $id );
							$category   = basename( dirname( $splFileInfo->getPathname() ) );

							$data = array(
								'id'          => $id,
								'class'       => $className,
								'category'    => ( $category == 'Widget' ) ? 'General' : $category,
								'title'       => $attributes['name'] ?: $widgetId,
								'description' => $attributes['description'] ?: 'No description found',
								'options'     => $attributes['options']
							);

							$path                  = array( $id => $data );
							self::$registry[ $id ] = $data;

						} else {
							continue;
						}

					} else {
						$dirName = $splFileInfo->getFilename();
						$path    = array( $dirName => array() );
					}

					for ( $depth = $iterator->getDepth() - 1; $depth >= 0; $depth -- ) {
						$dirName = $iterator->getSubIterator( $depth )->current()->getFilename();
						$path    = array( $dirName => $path );
					}

					uasort(
						$path, function ( $a, $b ) {
							return strcmp( $a['title'], $b['title'] );
						}
					);

					$r = array_merge_recursive( $r, $path );
				}
			}
			$this->_widgetList = $r;
		}

		return $this->_widgetList;
	}

	/**
	 *  Checks if a widget exists
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function widgetExist( $name ) {
		$name = strtolower( $name );

		if ( empty( self::$registry ) ) {
			$this->getWidgetList();
		}

		if ( isset( self::$registry[ $name ] ) ) {
			return self::$registry[ $name ];
		} else {
			return false;
		}
	}

	public static function getRegistry() {
		return self::$registry;
	}


}
