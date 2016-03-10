<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params'      => array(
                    'host'     => '87.230.13.184',
                    'port'     => '3306',
                    'user'     => 'vaboose_user',
                    'password' => 'vaboose_user',
                    'dbname'   => 'vaboose_merged',
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
