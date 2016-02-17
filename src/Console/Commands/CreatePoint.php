<?php

namespace Console\Commands;

use Lib\AbstractCommand;
use Models\Point\PointActiveRecord;
use Models\Point\PointModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatePoint
 *
 * @package Console\Commands
 */
class CreatePoint extends AbstractCommand
{
    protected function configure()
    {
        $this->setDescription('Creates new point')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'What is the name of a point?',
                'Point #1'
            )
            ->addArgument(
                'password',
                InputArgument::OPTIONAL,
                'What is the password of a application?',
                'password'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initialize($input, $output);

        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        /** @var PointModel $pointModel */
        $pointModel = $this->app['Models\Point\PointModel'];

        $pointArrayData = [
            'name' => $name,
            'password' => $password,
        ];

        $pointModel->create($pointArrayData);
        $this->writeAction(
            "{$pointArrayData['name']} has been successfully created!"
        );
    }
}
