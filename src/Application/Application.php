<?php

namespace Application;

class Application extends \Silex\Application
{
    /**
     * @return Application
     */
    public static function getInstance()
    {
        static $app = null;
        if (!$app) {
            $app = new self();
        }

        return $app;
    }

    public static function setUp()
    {
        $app = static::getInstance();

        return $app;
    }
}
