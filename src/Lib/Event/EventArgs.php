<?php

namespace Lib\Event;

/**
 * Class EventArgs
 *
 * This class is common data container for
 * event handlers
 *
 * @package Hatch\Core\Event
 */
class EventArgs
{
    public $message;
    public $data;

    /**
     * @param string $message
     * @param        $data
     */
    public function __construct($message = '', $data)
    {
        $this->message = $message;
        $this->data = $data;
    }
}
