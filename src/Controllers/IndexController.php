<?php

namespace Controllers;

use Lib\AbstractController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class IndexController extends AbstractController
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'Controllers\IndexController:indexAction');

        return $controllers;
    }

    public function indexAction()
    {
        return $this->response([
            'message' => 'Welcome to Workout Buddy API'
        ]);
    }

    // Internally forward requests to handle the trailing slash issue
    // @lnk https://github.com/silexphp/Silex/issues/149#issuecomment-10384486
    public function subRequestRedirectAction($resource)
    {
        // Now forward
        $subRequest = Request::create(
            '/'.$resource.'/',
            $this->app['request']->getMethod(),
            array_merge(
                $this->app['request']->query->all(),
                $this->app['request']->request->all()
            ),
            $this->app['request']->cookies->all(),
            $this->app['request']->files->all(),
            $this->app['request']->server->all(),
            $this->app['request']->getContent()
        );

        return $this->app->handle(
            $subRequest,
            HttpKernelInterface::SUB_REQUEST
        );
    }
}