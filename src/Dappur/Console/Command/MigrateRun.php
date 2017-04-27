<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Dappurware\CliUtils;

class MigrateRun extends Command
{
    protected function configure()
    {
        $this->setName('migrate:run')
            ->setDescription('Runs Phinx migration')
            ->setHelp('Runs the Phinx migration');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dappur = CliUtils::isDappur();
        if ($dappur) {

            $phinx_migrate = shell_exec('phinx migrate');
            $output->writeln($phinx_migrate);

        }


        
        
    }
}
