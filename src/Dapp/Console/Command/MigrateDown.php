<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Dappurware\CliUtils;

class MigrateDown extends Command
{
    protected function configure()
    {
        $this->setName('migrate:up')
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL, 'Which environment would you like to use?', null)
            ->addOption('target', 't', InputOption::VALUE_OPTIONAL, 'Which migration would you like to target?', null)
            ->setDescription('Runs Phinx migration')
            ->setHelp('Runs the Phinx migration');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getOption('environment');
        $target = $input->getOption('target');

        $dappur = CliUtils::isDappur();
        if ($dappur) {

            if ($target != null){
                $targetAppend = " -t " . $target;
            }else{
                $targetAppend = "";
            }

            if ($environment != null){
                $environmentAppend = " -e " . $environment;
            }else{
                $environmentAppend = "";
            }

            $phinx_migrate = shell_exec('phinx migrate'.$environmentAppend.$targetAppend);
            $output->writeln($phinx_migrate);
        } 
        
    }
}
