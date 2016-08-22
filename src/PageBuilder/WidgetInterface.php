<?php
namespace PageBuilder;

/**
 * Interface WidgetInterface
 * @package PageBuilder
 */
interface WidgetInterface
{
    public function render();

    public function getName();

    public function getDescription();

    public function getCategory();

    public function getOptions();

}

