<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Dappurware\CliUtils;

class MigrateCreate extends Command
{
    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Create a new Phinx migration.')
            ->addArgument('name', InputArgument::REQUIRED, 'What would you like to name this migration?')
            ->setHelp('Creates a new Phinx migration');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $dappur = CliUtils::isDappur();

        if (!CliUtils::isCamelCase($name)) {
            throw new \InvalidArgumentException('Migration needs to be CamelCaseFormat.');
        }

        if ($dappur) {

            $phinx_create = shell_exec('phinx create ' . $name);
            $output->writeln($phinx_create);

        }


        
        
    }
}
