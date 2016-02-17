<?php

namespace Lib;

use Silex\Application;
use Silex\ServiceProviderInterface;

abstract class AbstractService implements ServiceProviderInterface
{
    /** @var Application */
    protected $app;
    protected $injectionName = '';

    public function __construct()
    {
        $this->app = \Application\Application::getInstance();
    }

    public function register(Application $app)
    {
        $injectionService = get_called_class();
        $injectionName = $this->getInjectionName();

        $app[$injectionName] = $app->share(
            function () use ($app, $injectionService) {
                return new $injectionService();
            }
        );
    }

    public function boot(Application $app)
    {
        $injectionName = $this->getInjectionName();
        $app[$injectionName]->initialize();
    }

    protected function initialize()
    {
    }

    protected function getInjectionName()
    {
        $injectionService = get_called_class();
        return empty($this->injectionName) ?
            $injectionService : $this->injectionName;
    }
}