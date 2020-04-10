<?php
namespace PageBuilder\Config;

use Interop\Container\ContainerInterface;
use Laminas\Json\Expr;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class JqGridConfigFactory
 *
 * @package PageBuilder\Config
 */
class JqGridConfigFactory implements FactoryInterface
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return array
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $datetime = [
            'edittype'      => 'date',
            'editable'      => true,
            'hidden'        => true,
            'sorttype'      => 'date',
            'editrules'     => [
                'edithidden' => true,
                'required'   => false,
                'date'       => [
                    'datefmt' => self::DATE_FORMAT,
                ],
            ],
            'formatter'     => 'date',
            'formatoptions' => [
                'newformat' => self::DATE_FORMAT,
            ],
            'editoptions'   => [
                'format'     => self::DATE_FORMAT,
                'timeFormat' => 'hh:mm:ss',
                'region'     => 'en',
            ],
        ];

        $config = [
            'render_script_as_template' => false,
            'grid_options'              => [
                'ajaxGridOptions'    => [
                    'type' => 'GET',
                ],
                'toolbar'            => [
                    true,
                    'bottom',
                ],
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
            ],
            'excluded_columns'          => [
                'children',
                'password',
                'root',
                'siteTheme',
            ],
            'default_values'            => 'PageBuilder\DataProvider\GridDefault',
            'column_model'              => [
                'slug'             => [
                    'hidden'    => true,
                    'editrules' => [
                        'required'   => false,
                        'edithidden' => true,
                    ],
                ],
                'siteId'           => [
                    'hidden'    => true,
                    'editrules' => [
                        'required'   => false,
                        'edithidden' => true,
                    ],
                ],
                'label'            => [
                    'hidden'    => true,
                    'editrules' => [
                        'required'   => false,
                        'edithidden' => true,
                    ],
                ],
                'settingValue'     => [
                    'hidden' => false,
                ],
                'description'      => [
                    'hidden'    => true,
                    'editrules' => [
                        'required'   => false,
                        'edithidden' => true,
                    ],
                ],
                'timezone'         => [
                    'editrules'   => [
                        'required' => false,
                    ],
                    'editoptions' => [
                        'defaultValue' => 'UTC',
                    ],
                ],
                'cssId'            => [
                    'editrules' => [
                        'required' => false,
                    ],
                ],
                'parameters'       => [
                    'hidden'    => true,
                    'editrules' => [
                        'required'   => false,
                        'edithidden' => true,
                    ],
                ],
                'templates'        => [
                    'hidden'    => true,
                    'editrules' => [
                        'required'   => false,
                        'edithidden' => false,
                    ],
                ],
                'templateSections' => [
                    'hidden'          => true,
                    'editrules'       => [
                        'required'   => false,
                        'edithidden' => false,
                    ],
                    'isSubGridAsGrid' => true,
                ],
                'layout'           => [
                    'hidden' => true,
                ],
                'pageThemes'       => [
                    'isSubGridAsGrid' => true,
                ],
                'siteThemes'       => [
                    'isSubGridAsGrid' => true,
                ],
                'renewAt'          => $datetime + [
                        'editable' => true,
                    ],
                'updatedAt'        => $datetime + [
                        'editable' => true,

                    ],
                'createdAt'        => $datetime + [
                        'hidden' => false,
                    ],
                'startAt'          => $datetime,
                'endAt'            => $datetime,
            ],
            'edit_parameters'           => [
                'width'           => 550,
                'ajaxEditOptions' => [
                    'type' => 'PUT',
                ],
            ],
            'add_parameters'            => [
                'width' => 550,
            ],
            'delete_parameters'         => [
                'ajaxDelOptions' => [
                    'type' => 'DELETE',
                ],
            ],
            'toolbar_buttons'           => [
                /* Custom toolbar buttons */
                /** model specific */
                'specific' => [
                    'pageThemes' => [
                        'layout-manager' => [
                            'id'         => 'layman',
                            'class'      => 'btn btn-mini',
                            'title'      => 'Layout Manager',
                            'icon'       => 'icon-th-large',
                            'position'   => 'bottom',
                            'onLoad'     => '',
                            'callback'   => "function(){  if(synergyDataGrid.pageBuilder) { synergyDataGrid.pageBuilder.manageLayout(this) ; } }",
                            'attributes' => [
                                'data-endpoint'     => '/pagebuilder/layout/theme',
                                'data-entity'       => 'themes',
                                'data-template-url' => '/js/app/templates/layout-manager.html',
                            ],
                        ],
                    ],
                    'template'   => [
                        'layout-manager'  => [
                            'id'         => 'tempman',
                            'class'      => 'btn btn-mini',
                            'title'      => 'Template Manager',
                            'icon'       => 'icon-list',
                            'position'   => 'bottom',
                            'onLoad'     => '',
                            'callback'   => "function(){  if(synergyDataGrid.pageBuilder) { synergyDataGrid.pageBuilder.manageLayout(this) ; } }",
                            'attributes' => [
                                'data-entity'       => 'templates',
                                'data-endpoint'     => '/pagebuilder/layout/template',
                                'data-template-url' => '/js/app/templates/layout-manager.html',
                            ],
                        ],
                        'section-manager' => [
                            'id'         => 'sectionman',
                            'class'      => 'btn btn-mini',
                            'title'      => 'Sections',
                            'icon'       => 'icon-list',
                            'position'   => 'bottom',
                            'onLoad'     => '',
                            'callback'   => "function(){ if(synergyDataGrid.pageBuilder) {  synergyDataGrid.pageBuilder.manageSections(this) ; } }",
                            'attributes' => [
                                'data-entity'       => 'sections',
                                'data-href'         => '/pagebuilder/template/:id/sections',
                                'data-template-url' => '/js/app/templates/sections.html',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $config;
    }
}
