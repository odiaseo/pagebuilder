<?php

namespace PageBuilder;

use Zend\EventManager\EventInterface;

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
     * @param EventInterface $event
     *
     * @return mixed
     */
    public function setMvcEvent(EventInterface $event);
}
