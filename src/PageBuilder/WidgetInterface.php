<?php

namespace PageBuilder;

/**
 * Interface WidgetInterface.
 */
interface WidgetInterface
{
    /**
     * @return string
     */
    public function render();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getCategory();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param $text
     *
     * @return mixed
     */
    public function translate($text);
}
