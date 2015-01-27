<?php
namespace PageBuilder\Widget\Core;

use PageBuilder\BaseWidget;

class SmallGap extends BaseWidget
{
    protected $name = 'Small Gap';
    protected $description = 'Small Content spacer';
    protected $options = array();

    public function render()
    {
        return '<div class="gap gap-small"></div>';
    }
}
