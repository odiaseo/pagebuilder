<?php

return array(
    'router'          => array(
        'routes' => array(
            'pagebuilder\home'  => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/pagebuilder-app',
                    'defaults' => array(
                        'controller' => PageBuilder\Controller\IndexController::class,
                        'action'     => 'index',
                    ),
                ),
            ),
            'pagebuilder\admin' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/pagebuilder/admin',
                    'defaults' => array(
                        'controller' => PageBuilder\Controller\IndexController::class,
                        'action'     => 'admin',
                    ),
                ),
            ),
            'builder'           => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/layout/page[s][/:id]',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => \PageBuilder\Controller\PageController::class
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                )
            ),
            'template'          => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/layout/template[s][/:id]',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => \PageBuilder\Controller\TemplateController::class
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                )
            ),
            'template\section'  => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/template/:id/section[s]',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => \PageBuilder\Controller\TemplateSectionController::class
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                )
            ),
            'pagebuilder\crud'  => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/crud/:entity',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => \PageBuilder\Controller\IndexController::class,
                        'action'        => 'crud'
                    ),
                    'constraints' => array(
                        'entity' => '[a-z]+'
                    ),
                )
            ),
        ),
    ),
    'service_manager' => array(
        'shared'             => array(
            'jqgrid' => false
        ),
        'aliases'            => array(
            'pagebuilder\menu'           => 'PageBuilder\Navigation\NavigationFactory',
            'pagebuilder\service\layout' => 'PageBuilder\Service\LayoutService',
            'pagebuilder\widget\factory' => 'PageBuilder\WidgetFactory',
        ),
        'abstract_factories' => array(
            'PageBuilder\WidgetFactory',
            'PageBuilder\Model\AbstractModelFactory',
            'SynergyCommon\Model\AbstractModelFactory',
            'SynergyCommon\Service\AbstractServiceFactory',
            'SynergyCommon\Entity\AbstractEntityFactory',
        ),

        'invokables' => array(//'Zend\Session\SaveHandler\SaveHandlerInterface' => \PageBuilder\Session\CommonSessionHandler::class,
        ),
        'factories'  => array(
            'PageBuilder\Service\LayoutService'      => 'PageBuilder\Service\LayoutServiceFactory',
            'PageBuilder\DataProvider\GridDefault'   => 'PageBuilder\DataProvider\GridDefault',
            'PageBuilder\Config\JqGridConfigFactory' => 'PageBuilder\Config\JqGridConfigFactory',
            //'Zend\Session\SessionManager'            => 'Zend\Session\Service\SessionManagerFactory',
            //'Zend\Session\SaveHandler\SaveHandlerInterface' => DoctrineSessionSaveHandlerFactory::class,
        )
    ),
    'view_manager'    => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => include __DIR__ . '/../template_map.php',
        /*
        'template_map'             => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/partials/user-message' => __DIR__ . '/../view/layout/partials/user-message.phtml',
            'pagebuilder/index/index' => __DIR__ . '/../view/pagebuilder/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),*/
        'helper_map'               => array(
            'displayGrid' => 'PageBuilder\View\Helper\DisplayGrid',
        ),
        'template_path_stack'      => array(
            __DIR__ . '/../view',
        ),
        'strategies'               => array(
            'ViewJsonStrategy',
        ),
    ),
    'doctrine'        => array(
        'configuration' => array(
            'orm_default' => array(
                'driver'           => 'orm_default',
                'generate_proxies' => false,
                'proxy_dir'        => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'  => 'DoctrineORMModule\Proxy',
                'filters'          => array(
                    'site-specific' => 'SynergyCommon\Doctrine\Filter\SiteFilter',
                ),
            )
        ),
        'driver'        => array(
            'pagebuilder\entity\default' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/PageBuilder/Entity'
                )
            ),
            'pagebuilder\entity\join'    => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    __DIR__ . '/../src/PageBuilder/Entity/Join'
                )
            ),
            'synergy\common\entities'    => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    'vendor/synergy/common/src/SynergyCommon/Entity',
                )
            ),
            'orm_default'                => array(
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    'PageBuilder\Entity'      => 'pagebuilder\entity\default',
                    'PageBuilder\Entity\Join' => 'pagebuilder\entity\join',
                    'SynergyCommon\Entity'    => 'synergy\common\entities',

                )
            ),
        ),
        'eventmanager'  => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Translatable\TranslatableListener',
                    'Gedmo\Tree\TreeListener',
                    'Gedmo\Timestampable\TimestampableListener',
                    'SynergyCommon\Doctrine\Event\SluggableListener',
                    'SynergyCommon\Event\Listener\SiteAwareListener'
                )
            ),
        ),
    ),
    'translator'      => array(
        'locale'                    => 'en',
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ),
        ),
    ),
    'pagebuilder'     => array(
        'global_domain'     => 'rhemastudio.com',
        'bootstrap_version' => 2,
        'enabled'           => false, //change to true to enable
        'widgets'           => array(
            'paths' => array(
                'PageBuilder\Widget' => __DIR__ . '/../src/PageBuilder/Widget'
            )
        ),
        'main_navigation'   => 'pagebuilder\menu', // change this to the service alias for the main navigation menu
        //Overwrite default entities
        'entities'          => array(
            'page'      => 'PageBuilder\Entity\Page',
            'section'   => 'PageBuilder\Entity\Section',
            'template'  => 'PageBuilder\Entity\Template',
            'component' => 'PageBuilder\Entity\Component',
            'theme'     => 'PageBuilder\Entity\Theme',
            'site'      => 'PageBuilder\Entity\Site'
        ),
        //supported HTML tags
        'tags'              => array(
            'html'  => array(
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'a',
                'img',
                'sup',
                'span',
                'div',
                'strong',
                'button',
                'iframe',
                'i',
                'input',
                'li',
                'link',
                'label',
                'menu',
                'meta',
                'noscript',
                'cite',
                'object',
                'p',
                'pre',
                'script',
                'sub',
                'textarea',
                'title',
                'noframes',
                'map',
                'ins',
                'frame',
                'frameset',
                'code',
                'blockquote',
                'b',
                'area',
                'address',
                'samp',
                'small',
                'style',
                'ul'
            ),
            'form'  => array(
                'input',
                'form',
                'legend',
                'fieldset'
            ),
            'html5' => array(
                'audio',
                'video',
                'embed',
                'track',
                'source',
                'canvas',
                'aside',
                'article',
                'footer',
                'header',
                'nav',
                'section',
                'summary',
                'figure',
                'figcaption',
                'mark',
                'meter',
                'progress',
                'time',
                'dialog',
                'command',
                'output'
            )
        ),
        'css_classmap'      => array(
            'span'            => 'col-md-',
            'row-fluid'       => 'row',
            'icon-'           => 'glyphicon .glyphicon-',
            'container-fluid' => 'container',
            'input-large'     => 'input-lg',
            'input-xlarge'    => 'input-lg',
            'input-small'     => 'input-sm',
        ),
        'replacements'      => array(
            '[year]' => date('Y')
        ),
        'output_formatters' => array()

    ),
    'jqgrid'          => array(
        'factories' => array(
            'PageBuilder\Config\JqGridConfigFactory'
        )
    ),
    'synergy'         => array(
        'logger' => array(
            'directory' => __DIR__ . '/../data/logs',
            'namespace' => 'pagebuilder'
        )
    ),
    'super_sites'     => []
);
