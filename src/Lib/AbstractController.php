<?php

namespace Lib;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;

abstract class AbstractController extends AbstractService
    implements ControllerProviderInterface, ServiceProviderInterface
{
    protected function response(array $data, $status = 200, $headers = [])
    {
        return $this->app->json($data, $status, $headers);
    }
}