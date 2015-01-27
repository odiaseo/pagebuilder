<?php
namespace PageBuilder\Widget\Core;

use PageBuilder\BaseWidget;

class StatusMessages extends BaseWidget
{
    protected $name = 'Flash Message';
    protected $description = 'Displays status and error messages';

    public function __construct()
    {

    }

    public function render()
    {
        return $this->getView()->render('layout/partials/user-message.phtml');
    }
}
