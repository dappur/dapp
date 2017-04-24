<?php

namespace Dappur\CLI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Create a new Dappur project')
            ->addArgument('name', InputArgument::REQUIRED, 'Where would you like to create this project?')
            ->setHelp('Creates a new Dappur project');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get the migration path from the config
        $name = $input->getArgument('name');

        if(!preg_match('/^[a-zA-Z0-9_]+$/',$name)){
            throw new \InvalidArgumentException('Project folder names can be alpha-numeric with hyphens.');
        }

        $cwd = getcwd();

        $path = realpath($cwd . '/' . $name);

        if (file_exists($path)) {
            throw new \InvalidArgumentException('That folder name already exists.');
        }

        $output->writeln('Please wait while your project, ' . $name . ', is created...');

        $create_project = shell_exec('composer create-project dappur/framework ' . $name);

        $output->writeln($create_project);
        
    }
}
