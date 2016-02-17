<?php

namespace Lib\Event;

use Exception\Event\EventException;

/**
 * Class Eventable
 *
 * This trait allows to realise
 * observer pattern
 *
 * @package Hatch\Core\Event
 */
trait Eventable
{
    /**
     * events
     *
     * @var array
     */
    protected $events = array();

    /**
     * @param                       $eventName
     * @param EventHandlerContainer $eventHandler
     *
     * @throws EventException
     */
    public function attach(
        $eventName,
        EventHandlerContainer $eventHandler
    )
    {
        if ($this->hasEvent($eventName) !== true) {
            throw new EventException('Trying attach to unexisting event');
        }

        array_push($this->events[$eventName], $eventHandler);
    }

    /**
     * @param EventHandlerContainer $eventHandler
     */
    public function attachToAll(EventHandlerContainer $eventHandler)
    {
        foreach ($this->events as $key => $event) {
            array_push($this->events[$key], $eventHandler);
        }
    }

    /**
     * @param                       $eventName
     * @param EventHandlerContainer $eventHandler
     *
     * @throws EventException
     */
    public function detach(
        $eventName,
        EventHandlerContainer $eventHandler
    )
    {
        if ($this->hasEvent($eventName) !== true) {
            throw new EventException('Trying detach to unexisting event');
        }

        $key = array_search($eventHandler, $this->events[$eventName]);
        if ($key === false) {
            throw new EventException(
                'event handler doesn\'t exist for detaching'
            );
        }

        unset($this->events[$eventName][$key]);
    }

    /**
     * @param           $sender
     * @param           $eventName
     * @param EventArgs $eventArgs
     *
     * @throws EventException
     */
    public function callEvent($sender, $eventName, EventArgs $eventArgs)
    {
        if ($this->hasEvent($eventName) !== true) {
            throw new EventException('Trying to call unexising event');
        }

        /** @var EventHandlerContainer $eventHandler */
        foreach ($this->events[$eventName] as $eventHandler) {
            if ($eventHandler->exists() !== true) {
                throw new EventException('Calling unexisting method');
            }

            $eventHandler->call($sender, $eventArgs);
        }
    }

    /**
     * @param $eventName
     *
     * @return bool
     */
    public function hasEvent($eventName)
    {
        return isset($this->events[$eventName]);
    }

    /**
     * @param $eventName
     *
     * @throws EventException
     */
    protected function registerEvent($eventName)
    {
        if ($this->hasEvent($eventName)) {
            throw new EventException('Event already exists');
        }

        $this->events[$eventName] = array();
    }

    /**
     * @param array $eventNames
     */
    protected function registerEventRange(array $eventNames)
    {
        foreach ($eventNames as $eventName) {
            $this->registerEvent($eventName);
        }
    }

    protected function emitEvent($eventName, $eventMessage, $eventData)
    {
        $eventArgs = new EventArgs($eventMessage, $eventData);
        $this->callEvent($this, $eventName, $eventArgs);
    }
}
