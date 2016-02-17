<?php

namespace Lib;

use Application\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    /**
     * @var \Silex\Application;
     */
    protected $app;

    /** @var OutputInterface */
    protected $output;
    /** @var InputInterface */
    protected $input;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->app = Application::getInstance();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param string $actionContent
     */
    protected function writeAction($actionContent)
    {
        $this->output->writeln($actionContent);
    }
}
