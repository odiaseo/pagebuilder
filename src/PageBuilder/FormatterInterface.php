<?php

namespace PageBuilder;

/**
 * Class FormatterInterface
 *
 * Formats user defined components before rendering to the view
 *
 * @package PageBuilder
 */
interface FormatterInterface
{
    /**
     * @param  string $data
     *
     * @return string
     */
    public function format($data);
}