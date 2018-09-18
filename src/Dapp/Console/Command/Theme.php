<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Theme extends Command
{
    protected $tempDir;
    protected $themeTempFolder;


    protected function configure()
    {
        $this->setName('theme')
            ->setDescription('Installs official and custom themes into your Dappur project.')
            ->addArgument('theme', InputArgument::OPTIONAL, 'Theme git url')
            ->addOption('download-only', null, InputOption::VALUE_OPTIONAL, 'Downloads the theme, but does not change it in the database.', null)
            ->setHelp('Creates a new Dappur project');

        $this->tempDir = realpath(__DIR__.'/../../../../storage/temp');
        $this->themeTempFolder = uniqid();
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dappur = \Dappur\Dappurware\CliUtils::isDappur();

        // options and arguments
        $theme = $input->getArgument('theme');
        $noInstall = $input->getParameterOption('--download-only');

        if (!$theme) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Which theme would you like to install?',
                array('Frontend: Dappur', 'Dashboard: AdminLTE'),
                null
            );
            $question->setErrorMessage('Please select a theme.');

            $answer = $helper->ask($input, $output, $question);

            switch ($answer) {
                case 'Frontend: Dappur':
                    $theme = "git@github.com:dappur/theme-dappur.git";
                    break;
                case 'Dashboard: AdminLTE':
                    $theme = "git@github.com:dappur/theme-AdminLTE.git";
                    break;
                default:
                    break;
            }
        }


        $output->writeln('Validating Theme...');
        $themeJson = $this->validateTheme($theme, $this->themeTempFolder, $output);
        $themeType = $themeJson->type;

        if (!$themeType == "frontend" && !$themeType == "dashboard") {
            shell_exec("rm -rf {$this->tempDir}/*");
            touch("{$this->tempDir}/.gitkeep");
            throw new \InvalidArgumentException('Invalid or no theme type detected.');
        }

        $output->writeln($themeType . ' Theme Detected');
        $output->writeln('The `' . $themeJson->name . '` theme will be added to your project.');
        
        // Install Frontend Theme
        
        $install = shell_exec(
            "cp -r {$themeJson->directory} " . realpath(getcwd() . "/app/views/")
        );
        
        if ($noInstall) {
            $output->writeln('The `' . $themeJson->name . '` theme has been installed, but not activated.');
        }
        

        if (!$noInstall) {
            $output->writeln('Activating `' . $themeJson->name . '` theme...');
            if ($themeType == "frontend") {
                $siteTheme = $dappur->table('config')->where('name', 'theme')->update(["value" => $themeJson->name]);
            }

            if ($themeType == "dashboard") {
                $siteTheme = $dappur->table('config')->where('name', 'dashboard-theme')->update(["value" => $themeJson->name]);
            }
        }

        // Clean Up Temp Theme Files
        $output->writeln("Cleaning up temporary folder...");
        shell_exec("rm -rf {$this->tempDir}/*");
    }

    public function validateTheme($themeUrl, $tempFolder)
    {
        // validate themes
        $downloadRepo = shell_exec('cd '.$this->tempDir.' && git clone '.$themeUrl.' '.$tempFolder);

        // Check if repo downloaded
        if (!is_dir($this->tempDir . '/' . $tempFolder)) {
            throw new \InvalidArgumentException('Theme ' . $themeUrl . ' could not be downloaded.');
        }
        set_error_handler(function () { /**/ });
        $jsonOutput = json_decode(file_get_contents($this->tempDir . "/".$tempFolder."/theme.json"));
        restore_error_handler();
        
        if (!$jsonOutput) {
            throw new \InvalidArgumentException('theme.json file not detected.');
        }

        if (is_dir(getcwd() . '/app/views/' . $jsonOutput->name)) {
            throw new \InvalidArgumentException('It appears this theme is already installed.');
        }

        $jsonOutput->directory = realpath($this->tempDir . '/' . $tempFolder . '/' . $jsonOutput->name);

        if (!is_dir($jsonOutput->directory)) {
            throw new \InvalidArgumentException('Theme folder not found.');
        }

        return $jsonOutput;
    }
}
