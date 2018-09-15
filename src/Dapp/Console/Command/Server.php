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
        $this->setName('server')
            ->setDescription('Start a PHP Built-In Web Server')
            ->addArgument('port', InputArgument::OPTIONAL, 'What port would you like to run this off of?', 8181)
            ->setHelp('Starts a new php server for the project.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        CliUtils::isDappur();

        $port = $input->getArgument('port');

        if (!is_numeric($port)) {
            throw new \InvalidArgumentException('Port number is invalid.');
        }

        $output->writeln('Starting PHP server...' . PHP_EOL . 'http://localhost:' . $port);

        $server = shell_exec("php -S localhost:$port -t public/");

        $output->writeln($server);
        
    }
}
