<?php

namespace Dappur\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Console\Command;

class DappurApplication extends Application
{

    public function __construct($version = '0.6.0')
    {
        parent::__construct('Dappur - https://dappur.io.', $version);

        $this->addCommands(array(
            new Command\Create()
        ));
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // always show the version information except when the user invokes the help
        // command as that already does it
        if (false === $input->hasParameterOption(array('--help', '-h')) && null !== $input->getFirstArgument()) {
            $output->writeln($this->getLongVersion());
            $output->writeln('');
        }

        return parent::doRun($input, $output);
    }
}