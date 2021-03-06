<?php
namespace PageBuilder\Widget\Core;

use PageBuilder\BaseWidget;

class Gap extends BaseWidget
{
    protected $name = 'Gap';

    protected $description = 'Content spacer';

    protected $options = [];

    public function render()
    {
        return '<div class="gap"></div>';
    }
}
