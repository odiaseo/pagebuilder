<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

$datetime = array(
    'edittype'      => 'date',
    'editable'      => true,
    'hidden'        => true,
    'sorttype'      => 'date',
    'editrules'     => array(
        'edithidden' => true,
        'required'   => false,
        'date'       => array(
            'datefmt' => 'D, d M Y'
        )
    ),
    'formatter'     => 'date',
    'formatoptions' => array(
        'newformat' => 'D, d M Y'
    ),
    'editoptions'   => array(
        'format'     => 'D, d M Y',
        'timeFormat' => 'hh:mm:ss',
        'region'     => 'en',
    )
);

return array(

    'controllers'     => array(
        'invokables' => array(
            'PageBuilder\Controller\Page'            => 'PageBuilder\Controller\PageController',
            'PageBuilder\Controller\PageTheme'       => 'PageBuilder\Controller\PageThemeController',
            'PageBuilder\Controller\Index'           => 'PageBuilder\Controller\IndexController',
            'PageBuilder\Controller\TemplateSection' => 'PageBuilder\Controller\TemplateSectionController',
            'PageBuilder\Controller\Template'        => 'PageBuilder\Controller\TemplateController',
        ),
    ),

    'router'          => array(
        'routes' => array(
            'pagebuilder\home'  => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/pagebuilder-app',
                    'defaults' => array(
                        'controller' => 'PageBuilder\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'pagebuilder\admin' => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/pagebuilder/admin',
                    'defaults' => array(
                        'controller' => 'PageBuilder\Controller\Index',
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
                        'controller'    => 'PageBuilder\Controller\Page'
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                )
            ),
            'builder\theme'     => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/pagebuilder/layout/theme[/:id]',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'PageBuilder\Controller',
                        'module'        => 'page-builder',
                        'controller'    => 'PageBuilder\Controller\PageTheme'
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
                        'controller'    => 'PageBuilder\Controller\Template'
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
                        'controller'    => 'PageBuilder\Controller\TemplateSection'
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
            'PageBuilder\Util\Widget'            => 'PageBuilder\Util\Widget',

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
                    'vendor/synergy/common/lib/SynergyCommon/Entity',
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
                    'Gedmo\Tree\TreeListener',
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener',
                    'SynergyCommon\Event\Listener\SiteAwareListener'
                )
            ),
        ),
    ),

    'pagebuilder'     => array(
        'enabled'           => false, //change to true to enable
        'modules'           => array(
            'PageBuilder'
        ),
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
        ),
        'css_classmap'      => array(
            'span1'           => 'span1',
            'span2'           => 'span2',
            'span3'           => 'span3',
            'span4'           => 'span4',
            'span5'           => 'span5',
            'span6'           => 'span6',
            'span7'           => 'span7',
            'span8'           => 'span8',
            'span9'           => 'span9',
            'span10'          => 'span10',
            'span11'          => 'span11',
            'span12'          => 'span12',
            'row'             => 'row',
            'row-fluid'       => 'row-fluid',
            'container'       => 'container',
            'container-fluid' => 'container-fluid'
        ),
        'replacements'      => array(
            '[year]' => date('Y')
        ),
        'output_formatters' => array()

    ),
    'jqgrid'          => array(
        'render_script_as_template' => false,
        'grid_options'              => array(
            'ajaxGridOptions'    => array(
                'type' => 'GET'
            ),
            'toolbar'            => array(
                true,
                'bottom'
            ),
            'gridview'           => true,
            'allowResizeColumns' => false,
            'forceFit'           => true,
            'shrinkToFit'        => true,
            'rownumbers'         => true,
            'onSelectRow'        => (string)new \Zend\Json\Expr("function (rowId) {
                var grid = $(this);
                var gsr = grid.data('lastsel');
                if (rowId && rowId != gsr) {
                    grid.restoreRow(gsr);
                    grid.data('lastsel', rowId);
                }
            }"),
        ),
        'excluded_columns'          => array(
            'children',
            'password',
            'root',
            'siteTheme'
        ),
        'column_model'              => array(
            'slug'             => array(
                'hidden'    => true,
                'editrules' => array(
                    'required'   => false,
                    'edithidden' => true
                )
            ),
            'siteId'           => array(
                'hidden'    => true,
                'editrules' => array(
                    'required'   => false,
                    'edithidden' => true
                )
            ),
            'label'            => array(
                'hidden'    => true,
                'editrules' => array(
                    'required'   => false,
                    'edithidden' => true
                )
            ),
            'description'      => array(
                'hidden'    => true,
                'editrules' => array(
                    'required'   => false,
                    'edithidden' => true
                )
            ),
            'timezone'         => array(
                'editrules'   => array(
                    'required' => false,
                ),
                'editoptions' => array(
                    'defaultValue' => 'UTC',
                ),
            ),
            'cssId'            => array(
                'editrules' => array(
                    'required' => false,
                )
            ),
            'parameters'       => array(
                'hidden'    => true,
                'editrules' => array(
                    'required'   => false,
                    'edithidden' => true
                )
            ),
            'templates'        => array(
                'hidden'    => true,
                'editrules' => array(
                    'required'   => false,
                    'edithidden' => false
                )
            ),
            'templateSections' => array(
                'hidden'          => true,
                'editrules'       => array(
                    'required'   => false,
                    'edithidden' => false
                ),
                'isSubGridAsGrid' => true,
            ),
            'layout'           => array(
                'hidden' => true
            ),
            'pageThemes'       => array(
                'isSubGridAsGrid' => true,
            ),

            'siteThemes'       => array(
                'isSubGridAsGrid' => true,
            ),
            'renewAt'          => $datetime + array(
                'editable' => true
            ),
            'updatedAt'        => $datetime + array(
                'editable' => true,

            ),
            'createdAt'        => $datetime + array(
                'hidden' => false,
            ),
            'startAt'          => $datetime,
            'endAt'            => $datetime,
        ),
        'edit_parameters'           => array(
            'width'           => 550,
            'ajaxEditOptions' => array(
                'type' => 'PUT'
            )
        ),
        'add_parameters'            => array(
            'width' => 550,
        ),
        'delete_parameters'         => array(
            'ajaxDelOptions' => array(
                'type' => 'DELETE'
            )
        ),
        'toolbar_buttons'           => array( /* Custom toolbar buttons */

            /** model specific */
            'specific' => array(
                'pageThemes' => array(
                    'layout-manager' => array(
                        'id'         => 'layman',
                        'class'      => 'btn btn-mini',
                        'title'      => 'Layout Manager',
                        'icon'       => 'icon-th-large',
                        'position'   => 'bottom',
                        'onLoad'     => '',
                        'callback'   => "function(){  if(synergyDataGrid.pageBuilder) { synergyDataGrid.pageBuilder.manageLayout(this) ; } }",
                        'attributes' => array(
                            'data-endpoint'     => '/pagebuilder/layout/theme',
                            'data-entity'       => 'themes',
                            'data-template-url' => '/js/app/templates/layout-manager.html'
                        )
                    )
                ),
                'template'   => array(
                    'layout-manager'  => array(
                        'id'         => 'tempman',
                        'class'      => 'btn btn-mini',
                        'title'      => 'Template Manager',
                        'icon'       => 'icon-list',
                        'position'   => 'bottom',
                        'onLoad'     => '',
                        'callback'   => "function(){  if(synergyDataGrid.pageBuilder) { synergyDataGrid.pageBuilder.manageLayout(this) ; } }",
                        'attributes' => array(
                            'data-entity'       => 'templates',
                            'data-endpoint'     => '/pagebuilder/layout/template',
                            'data-template-url' => '/js/app/templates/layout-manager.html'
                        )
                    ),
                    'section-manager' => array(
                        'id'         => 'sectionman',
                        'class'      => 'btn btn-mini',
                        'title'      => 'Sections',
                        'icon'       => 'icon-list',
                        'position'   => 'bottom',
                        'onLoad'     => '',
                        'callback'   => "function(){ if(synergyDataGrid.pageBuilder) {  synergyDataGrid.pageBuilder.manageSections(this) ; } }",
                        'attributes' => array(
                            'data-entity'       => 'sections',
                            'data-href'         => '/pagebuilder/template/:id/sections',
                            'data-template-url' => '/js/app/templates/sections.html'
                        )
                    ),
                ),
            )
        ),
    ),
    'synergy'         => array(
        'logger' => array(
            'directory' => __DIR__ . '/../data/logs',
            'namespace' => 'pagebuilder'
        )
    )
);
