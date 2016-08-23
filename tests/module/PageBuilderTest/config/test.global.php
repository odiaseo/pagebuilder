<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params'      => array(
                    'driver'   => 'pdo_sqlite',
                    'host'     => '127.0.0.1',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'password',
                    'path'     => sys_get_temp_dir() . '/sqlite.db',
                ),
            ),
        )

    ),
    'jqgrid'   => array(
        'compress_script' > true,
        'grid_model'      => array(
            'testBrands' => array(
                'isSubGridAsGrid' => true
            )
        ),
        'toolbar_buttons' => array(
            'global' => array(
                'help' => array(
                    'title'      => 'Help',
                    'icon'       => 'icon-info-sign',
                    'position'   => 'top',
                    'class'      => 'btn btn-mini',
                    'callback'   => new \Zend\Json\Expr('function(){ alert("i am here");}'),
                    'onLoad'     => 'var home = "";',
                    'attributes' => array(
                        'data - entity' => 'templates',
                        'data - href'   => 'my_url',
                    )
                )
            )
        )
    ),
);
