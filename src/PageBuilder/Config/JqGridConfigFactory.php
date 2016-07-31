<?php
namespace PageBuilder\Config;

use Zend\Json\Expr;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class JqGridConfigFactory
 *
 * @package PageBuilder\Config
 */
class JqGridConfigFactory implements FactoryInterface
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $datetime = array(
            'edittype'      => 'date',
            'editable'      => true,
            'hidden'        => true,
            'sorttype'      => 'date',
            'editrules'     => array(
                'edithidden' => true,
                'required'   => false,
                'date'       => array(
                    'datefmt' => self::DATE_FORMAT
                )
            ),
            'formatter'     => 'date',
            'formatoptions' => array(
                'newformat' => self::DATE_FORMAT
            ),
            'editoptions'   => array(
                'format'     => self::DATE_FORMAT,
                'timeFormat' => 'hh:mm:ss',
                'region'     => 'en',
            )
        );

        $config = array(
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
                'onSelectRow'        => (string)new Expr(
                    "function (rowId) {
                var grid = $(this);
                var gsr = grid.data('lastsel');
                if (rowId && rowId != gsr) {
                    grid.restoreRow(gsr);
                    grid.data('lastsel', rowId);
                }
            }"
                ),
            ),
            'excluded_columns'          => array(
                'children',
                'password',
                'root',
                'siteTheme'
            ),
            'default_values'            => 'PageBuilder\DataProvider\GridDefault',
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
                'settingValue' => array(
                    'hidden' => false
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
            'toolbar_buttons'           => array(
                /* Custom toolbar buttons */
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
        );

        return $config;
    }
}
