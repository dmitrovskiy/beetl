<?php

namespace Lib\Event;

use Exception\Event\EventHandlerContainerException;

/**
 * Class EventHandlerContainer
 *
 * This class is needed to store
 * event handler
 *
 * @package Hatch\Core\Event
 */
class EventHandlerContainer
{
    protected $instance;
    protected $methodName;

    /**
     * @param $instance
     * @param $methodName
     *
     * @throws EventHandlerContainerException
     */
    public function __construct($instance, $methodName)
    {
        $this->setInstance($instance);
        $this->setMethod($methodName);
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param $instance
     *
     * @throws EventHandlerContainerException
     */
    public function setInstance($instance)
    {
        if (empty($instance)) {
            throw new EventHandlerContainerException(
                'Instance name must not be empty'
            );
        }

        if (is_object($instance) !== true) {
            throw new EventHandlerContainerException(
                'Instance isn\'t object'
            );
        }

        $this->instance = $instance;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->methodName;
    }

    /**
     * @param string $methodName
     *
     * @throws EventHandlerContainerException
     */
    public function setMethod($methodName)
    {
        if (empty($methodName)) {
            throw new EventHandlerContainerException(
                'Method name must not be empty'
            );
        }

        if (is_string($methodName) !== true) {
            throw new EventHandlerContainerException(
                'Method should be a string'
            );
        }

        $this->methodName = $methodName;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return method_exists($this->instance, $this->methodName);
    }

    /**
     * @param           $sender
     * @param EventArgs $eventArgs
     */
    public function call($sender, EventArgs $eventArgs)
    {
        call_user_func_array(
            array($this->instance, $this->methodName),
            array($sender, $eventArgs)
        );
    }
}
