<?php

namespace Console\Commands;

use Lib\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Index extends AbstractCommand
{
    protected function configure()
    {
        $this->setDescription('Test command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Welcome to Workout Buddy API environment");
    }
}
