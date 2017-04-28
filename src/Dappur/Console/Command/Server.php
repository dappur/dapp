<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Dappurware\CliUtils;

class Server extends Command
{
    protected function configure()
    {
        $this->setName('start')
            ->setDescription('Start a PHP server')
            ->setHelp('Starts a new php server for the project.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('Starting PHP server...' . PHP_EOL . 'http://localhost:8181');

        $server = shell_exec('php -S localhost:8181 -t public/');

        $output->writeln($server);
        
    }
}
