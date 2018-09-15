<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Dappurware\CliUtils;

class MigrateBreakpoint extends Command
{
    protected function configure()
    {
        $this->setName('breakpoint')
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL, 'Which environment would you like to use?', null)
            ->addOption('target', 't', InputOption::VALUE_OPTIONAL, 'Which migration would you like to target?', null)
            ->addOption('remove-all', 'r', InputOption::VALUE_OPTIONAL, 'Remove all breakpoints.', null)
            ->setDescription('Rolls Back Phinx migration')
            ->setHelp('Rolls Back Phinx migration');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getOption('environment');
        $target = $input->getOption('target');
        $removeAll = $input->getOption('remove-all');

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

            if ($removeAll != null){
                $removeAllAppend = " -r";
            }else{
                $removeAllAppend = "";
            }

            $phinx_migrate = shell_exec('phinx breapoint'.$environmentAppend.$targetAppend.$removeAllAppend);
            $output->writeln($phinx_migrate);
        } 
        
    }
}
