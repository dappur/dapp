<?php

namespace Dappur\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Console\Command;

class DappApplication extends Application
{
    public function __construct(
        $version = json_decode(file_get_contents(__DIR__ . '/../../../composer.json'))->name->version
    ) {
        parent::__construct('Dappur - https://dappur.io.', $version);

        $this->addCommands(array(
            new Command\Create(),
            new Command\Setup(),
            new Command\Addon(),
            new Command\Controller(),
            new Command\MigrateCreate(),
            new Command\MigrateUp(),
            new Command\MigrateDown(),
            new Command\Server()
        ));
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        return parent::doRun($input, $output);
    }

    public function checkIfDappur()
    {
        return true;
    }
}
