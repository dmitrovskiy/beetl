<?php

namespace Application;

use Controllers\AuthenticationController;
use Controllers\IndexController;
use Controllers\PointsController;
use Controllers\TokenController;
use Controllers\UsersController;
use Exception\ConfigException;
use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Lib\ResourceLoader\ResourceLoaderInterface;
use Middleware\Common\JsonBodyParser;
use Middleware\Firewall\UserSession;
use Models\Application\ApplicationModel;
use Models\Application\ApplicationNormalizer;
use Models\Point\PointActiveRecord;
use Models\Point\PointModel;
use Models\Point\PointNormalizer;
use Models\Token\TokenModel;
use Models\Token\TokenNormalizer;
use Models\User\UserModel;
use Models\User\UserNormalizer;
use Services\AuthenticationService;
use Services\EncoderService;
use Services\FacebookService;
use Services\FileProcessor\CloudinaryFileProcessor;
use Services\SessionService;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Wildsurfer\Provider\MongodmServiceProvider;

class Boot
{
    /** @var Application */
    protected $app;

    /** @var ResourceLoaderInterface */
    protected $configLoader;

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function setConfigLoader($configLoader)
    {
        $this->configLoader = $configLoader;
    }

    public function checkConfig(array $configList)
    {
        foreach ($configList as $value) {
            if (!$this->configLoader->hasResource($value)) {
                throw new ConfigException("Config isn't serviceable. Try to add $value variable");
            }
        }
    }

    public function registerServices()
    {
        date_default_timezone_set('UTC');

        $this->app['configLoader'] = $this->configLoader;

        $this->app->register(new UrlGeneratorServiceProvider());
        $this->app->register(new ValidatorServiceProvider());
        $this->app->register(new ServiceControllerServiceProvider());
        $this->app->register(new HttpFragmentServiceProvider());

        // CORS support
        $this->app->register(new CorsServiceProvider(), ["cors.allowOrigin" => "*"]);

        $this->app->register(new IndexController());
        $this->app->register(new JsonBodyParser());
        $this->app->register(new PointsController());
        $this->app->register(new PointModel());
        $this->app->register(new PointNormalizer());

        $this->app->register(
            new MongodmServiceProvider(),
            [
                'mongodm.blocks' => [
                    'default' => [
                        'host' => $this->configLoader->getResource('DB_HOST'),
                        'db' => $this->configLoader->getResource('DB_NAME'),
                        'options' => $this->configLoader->getResource('DB_OPTIONS')
                    ]
                ]
            ]
        );
        $this->app['mongodm']->connect();
    }

    public function mountControllers()
    {
        $this->app->after($this->app['cors']);
        $this->app->before('Middleware\Common\JsonBodyParser:parse');

        // Internally forward requests to handle the trailing slash issue
        // @lnk https://github.com/silexphp/Silex/issues/149#issuecomment-10384486
        $this->app->match(
            '/{resource}',
            'Controllers\IndexController:subRequestRedirectAction'
        );

        $this->app->mount('/', new IndexController());
        $this->app->mount('/points', new PointsController());
    }
}