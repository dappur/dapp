<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    protected $tempDir;
    protected $siteThemeTemp;
    protected $dashboardThemeTemp;


    protected function configure()
    {
        $this->setName('new')
            ->setDescription('Create a new Dappur project')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of project')
            ->addOption('theme', 't', InputOption::VALUE_OPTIONAL, 'Frontend theme git url', "git@github.com:dappur/theme-dappur.git")
            ->addOption('dashboard', 'd', InputOption::VALUE_OPTIONAL, 'Dashboard theme git url', "git@github.com:dappur/theme-AdminLTE.git")
            ->addOption('vagrant', null, InputOption::VALUE_OPTIONAL, 'Run `vagrant up` when finished installing', null)
            ->setHelp('Creates a new Dappur project');

        $this->tempDir = realpath(__DIR__.'/../../../../storage/temp');
        $this->siteThemeTemp = uniqid();
        $this->dashboardThemeTemp = uniqid();
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // options and arguments
        $name = $input->getArgument('name');
        $siteTheme = $input->getOption('theme');
        $dashboardTheme = $input->getOption('dashboard');
        $vagrant = $input->getParameterOption('--vagrant');

        $output->writeln('Please wait while your project, ' . $name . ', is created...');

        // Check for existing project name
        if (file_exists(realpath(getcwd() . '/' . $name))) {
            throw new \InvalidArgumentException('That folder name already exists.');
        }

        // Validate Frontend Theme
        $output->writeln('Validating Frontend Theme...');
        $siteJson = $this->validateTheme($siteTheme, $this->siteThemeTemp, $output);

        // Validate Dashboard Theme
        $output->writeln('Validating Dashboard Theme...');
        $dashboardJson = $this->validateTheme($dashboardTheme, $this->dashboardThemeTemp, $output);
        $output->writeln('Themes validated...');

        if (!preg_match('/^([A-Za-z0-9]+-)*[A-Za-z0-9]+$/i', $name)) {
            throw new \InvalidArgumentException('Project names can be alpha-numeric with hyphens.');
        }

        // Create the project via composer
        $create_project = shell_exec('composer create-project --no-install dappur/framework ' . $name);
        $output->writeln($create_project);

        // Install Frontend Theme
        $siteThemeInstall = shell_exec(
            "cp -r {$siteJson->directory} " . realpath(getcwd() . "/$name/app/views/")
        );
        $migration = getcwd() . "/$name" . '/database/migrations/20170118012924_init_database.php';
        $migrationContents = file_get_contents($migration);
        $migrationContents = str_replace("array(1, 'theme', 'Site Theme', 3, 'dappur'),", "array(1, 'theme', 'Site Theme', 3, '".$siteJson->name."'),", $migrationContents);
        file_put_contents($migration, $migrationContents);

        $output->writeln("Frontend theme successfully installed.");
        // Install Dashboard Theme
        $dashboardThemeInstall = shell_exec(
            "cp -r {$dashboardJson->directory} " . realpath(getcwd() . "/$name/app/views/")
        );
        $migration = getcwd() . "/$name" . '/database/migrations/20170118012924_init_database.php';
        $migrationContents = file_get_contents($migration);
        $migrationContents = str_replace("array(2, 'dashboard-theme', 'Dashboard Theme', 3, 'AdminLTE'),", "array(2, 'dashboard-theme', 'Dashboard Theme', 3, '".$dashboardJson->name."'),", $migrationContents);
        file_put_contents($migration, $migrationContents);
        $output->writeln("Dashboard theme successfully installed.");

        // Clean Up Temp Theme Files
        $output->writeln("Cleaning up temporary folder...");
        shell_exec("rm -rf {$this->tempDir}/*");
        touch("{$this->tempDir}/.gitkeep");

        // Prepare settings.json
        $settingsJson = json_decode(file_get_contents(getcwd() . "/$name/settings.json.dist"));
        $settingsJson->framework = $name;
        file_put_contents(getcwd() . "/$name/settings.json", json_encode($settingsJson, JSON_PRETTY_PRINT));

        // Install Composer Vendor Files
        $composerInstall = shell_exec("cd $name && composer install");
        $output->writeln($composerInstall);

        $output->writeln($name . ' has been successfully installed.');

        // Run vagrant up if set
        if (is_null($vagrant)) {
            $vagrantUp = shell_exec("cd $name && vagrant up");
            $output->writeln($vagrantUp);
        }
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

        $jsonOutput->directory = realpath($this->tempDir . '/' . $tempFolder . '/' . $jsonOutput->name);

        if (!is_dir($jsonOutput->directory)) {
            throw new \InvalidArgumentException('Theme folder not found.');
        }

        return $jsonOutput;
    }
}
