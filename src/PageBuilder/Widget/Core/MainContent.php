<?php
    namespace PageBuilder\Widget\Core;

    use PageBuilder\BaseWidget;
    use PageBuilder\View\Helper\PageBuilder;

    class MainContent extends BaseWidget
    {
        protected $name = 'Main Content';
        protected $description = 'Displays the main content of the page';

        public function __construct()
        {

        }

        public function render()
        {
            return '{{' . PageBuilder::MAIN_CONTENT . '}}';
        }
    }