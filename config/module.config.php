<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(

    'controllers'     => array(
        'invokables' => array(
            'PageBuilder\Controller\Page'            => 'PageBuilder\Controller\PageController',
            'PageBuilder\Controller\Index'           => 'PageBuilder\Controller\IndexController',
            'PageBuilder\Controller\TemplateSection' => 'PageBuilder\Controller\TemplateSectionController',
            'PageBuilder\Controller\Template'        => 'PageBuilder\Controller\TemplateController',
        ),
    ),

    'router'          => array(
        'routes' => array(
            'home'             => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'PageBuilder\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'builder'          => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/layout/page[s][/:id]',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => 'PageBuilder\Controller\Page'
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                )
            ),
            'template'         => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/layout/template[s][/:id]',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => 'PageBuilder\Controller\Template'
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                )
            ),
            'template\section' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/template/:id/section[s]',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => 'PageBuilder\Controller\TemplateSection'
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                )
            ),
            'pagebuilder\crud' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/crud/:entity',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => 'PageBuilder\Controller\Index',
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
            'pagebuilder\service\layout' => 'PageBuilder\Service\LayoutService'
        ),
        'abstract_factories' => array(
            'PageBuilder\WidgetFactory',
            'PageBuilder\Model\AbstractModelFactory'
        ),
        'invokables'         => array(
            'pagebuilder\entity\component'       => 'PageBuilder\Entity\Component',
            'pagebuilder\entity\page'            => 'PageBuilder\Entity\Page',
            'pagebuilder\entity\section'         => 'PageBuilder\Entity\Section',
            'pagebuilder\entity\site'            => 'PageBuilder\Entity\Site',
            'pagebuilder\entity\template'        => 'PageBuilder\Entity\Template',
            'pagebuilder\entity\theme'           => 'PageBuilder\Entity\Theme',
            'pagebuilder\entity\pageTheme'       => 'PageBuilder\Entity\Join\PageTheme',
            'pagebuilder\entity\siteTheme'       => 'PageBuilder\Entity\Join\SiteTheme',
            'pagebuilder\entity\templateSection' => 'PageBuilder\Entity\Join\TemplateSection',

            //Services
            'PageBuilder\Service\LayoutService'  => 'PageBuilder\Service\LayoutService'
        )
    ),

    'view_manager'    => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'pagebuilder/index/index' => __DIR__ . '/../view/pagebuilder/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
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

    'widgets'         => array(
        'directory_location' => array(
            __DIR__ . '/../src/PageBuilder/Widget'
        )
    ),
    'doctrine'        => array(
        'configuration' => array(
            'orm_default' => array(
                'driver'           => 'orm_default',
                'generate_proxies' => true,
                'proxy_dir'        => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'  => 'DoctrineORMModule\Proxy',
                'filters'          => array(
                    'site-specific' => 'PageBuilder\Filter\SiteFilter',
                ),
            )
        ),
        'driver'        => array(
            'app_entity_default' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/PageBuilder/Entity')
            ),
            'app_entity_join'    => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/PageBuilder/Entity/Join')
            ),
            'orm_default'        => array(
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    'PageBuilder\Entity'      => 'app_entity_default',
                    'PageBuilder\Entity\Join' => 'app_entity_join'
                )
            )
        ),
        'eventmanager'  => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Tree\TreeListener',
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                )
            ),
        ),
    ),
    'pagebuilder'     => array(
        //Overwrite default entities
        'entities' => array(
            'page'      => 'PageBuilder\Entity\Page',
            'section'   => 'PageBuilder\Entity\Section',
            'template'  => 'PageBuilder\Entity\Template',
            'component' => 'PageBuilder\Entity\Component',
            'theme'     => 'PageBuilder\Entity\Theme',
            'site'      => 'PageBuilder\Entity\Site'
        ),
        'tags'     => array(
            'html'  => array(
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'img', 'sup',
                'span', 'div', 'strong', 'button', 'iframe', 'i', 'input',
                'link', 'label', 'menu', 'meta', 'noscript', 'cite',
                'object', 'p', 'pre', 'script', 'sub', 'textarea', 'title',
                'noframes', 'map', 'ins', 'frame', 'frameset', 'code', 'blockquote',
                'b', 'area', 'address', 'samp', 'small', 'style'
            ),
            'form'  => array(
                'input', 'form', 'legend', 'fieldset'
            ),
            'html5' => array(
                'audio', 'video', 'embed', 'track', 'source', 'canvas',
                'aside', 'footer', 'header', 'nav', 'section', 'summary',
                'figure', 'figcaption', 'mark', 'meter', 'progress', 'time',
                'dialog', 'command', 'output'
            )
        )
    )
);
