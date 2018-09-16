<?php

namespace Dappur\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Console\Command;

class DappApplication extends Application
{
    public function __construct($version = "0.7.0")
    {
        parent::__construct('Dappur - https://dappur.io.', $version);

        $this->addCommands(array(
            new Command\Create(),
            new Command\Setup(),
            new Command\Controller(),
            new Command\MigrateCreate(),
            new Command\MigrateUp(),
            new Command\MigrateDown(),
            new Command\MigrateBreakpoint(),
            new Command\Server(),
            new Command\TwigEx(),
            new Command\App(),
            new Command\Middleware()
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
