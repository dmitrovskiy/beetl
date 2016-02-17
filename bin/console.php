#!/usr/bin/env php
<?php
include_once __DIR__ . '/../vendor/autoload.php';

set_time_limit(0);

try {
    $app = \Application\Application::setUp();

    // Setting up the application
    $boot = new \Application\Boot();
    $boot->setApp($app);

    $boot->setConfigLoader(new \Lib\ResourceLoader\ConfigLoader());
    $configList = new \Application\ConfigList();
    $boot->checkConfig($configList->getConfigList());

    $boot->registerServices();
    $boot->mountControllers();

    $cli_app = new \Console\Application($app);
    $cli_app->run();
} catch (\Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['message: ' => $e->getMessage()]);
    throw $e;
}
