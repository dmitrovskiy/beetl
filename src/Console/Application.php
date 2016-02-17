<?php

namespace Console;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * @var \Application\Application
     */
    protected $app;

    public function __construct($app)
    {
        parent::__construct('CLI Application', '0.0.1');
        $this->app = $app;
    }

    public function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $path = __DIR__."/Commands";
        $commandFiles = glob($path);

        foreach ($commandFiles as $commandFile) {
            if ($handle = opendir($commandFile)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $commandName = basename($entry, '.php');
                        $name = "Console\\Commands\\$commandName";
                        array_push($commands, new $name($commandName));
                    }
                }
                closedir($handle);
            }
        }

        return $commands;
    }
}
