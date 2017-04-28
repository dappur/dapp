<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Dappur\Dappurware\CliUtils;

class Addon extends Command
{
    protected function configure()
    {
        $this->setName('addon')
            ->setDescription('Install a dappur addon')
            ->addArgument('name', InputArgument::REQUIRED, 'What addon would you like to add (blog)?')
            ->setHelp('Installs a Dappur addon.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        CliUtils::isDappur();

        // get the migration path from the config
        $name = $input->getArgument('name');

        switch ($name) {
            case 'blog':
                $helper = $this->getHelper('question');
                $blog_question = new Question('Would you like to install the Dappur blog addon? (y/n): ', false);
                $blog_question->setValidator(function ($answer) {
                    if (!preg_match('/^(y|j)/i', $answer)) {
                        return false;
                    }

                    return true;
                });
                $bloganswer = $helper->ask($input, $output, $blog_question);

                if (!$bloganswer) {
                    throw new \InvalidArgumentException('You cancelled the blog addon installation.');
                }

                $blogadmin = getcwd() . '/app/routes/admin-blog.php';

                if (file_exists($blogadmin)){
                    throw new \InvalidArgumentException('The Dappur blog addon appears to have already been installed.');
                }

                $require_blog = shell_exec('composer require dappur/addon-blog');
                $output->writeln($require_blog);

                function recurseCopy($src,$dst) { 
                    $dir = opendir($src); 
                    @mkdir($dst); 
                    while(false !== ( $file = readdir($dir)) ) { 
                        if (( $file != '.' ) && ( $file != '..' )) { 
                            if ( is_dir($src . '/' . $file) ) { 
                                recurseCopy($src . '/' . $file,$dst . '/' . $file); 
                            } 
                            else { 
                                copy($src . '/' . $file,$dst . '/' . $file); 
                            } 
                        } 
                    } 
                    closedir($dir); 
                }

                $output->writeln("Copying files for admin and views...");
                recurseCopy(getcwd() . '/vendor/dappur/addon-blog/app', getcwd() . '/app');
                recurseCopy(getcwd() . '/vendor/dappur/addon-blog/public', getcwd() . '/public');
                recurseCopy(getcwd() . '/vendor/dappur/addon-blog/database/migrations', getcwd() . '/database/migrations');

                $output->writeln("Running database migration...");
                $migrate = shell_exec('phinx migrate');
                $output->writeln($migrate);

                $output->writeln("Blog added successfully!");

                $containerBootstrap = getcwd() . '/app/bootstrap/controllers.php';
                $containerAppend = file_get_contents(__DIR__ . "/../../../../templates/controller-append.tpl");

                $append = file_put_contents($containerBootstrap, str_replace("{{CONTROLLER_NAME}}", "BlogController", $containerAppend), FILE_APPEND | LOCK_EX);

                $dependenciesBootstrap = getcwd() . '/app/bootstrap/dependencies.php';
                $dependenciesAppend = file_get_contents(__DIR__ . "/../../../../templates/dependencies-blog.tpl");

                $append = file_put_contents($dependenciesBootstrap, $dependenciesAppend, FILE_APPEND | LOCK_EX);

                $sidebar_path = getcwd() . "/app/views/Default/Admin/inc/sidebar.twig";
                $sidebarInsert = file_get_contents(__DIR__ . "/../../../../templates/admin-sidebar-blog.tpl");


                file_put_contents($sidebar_path, str_replace('{% if auth.hasAccess(\'user.create\') %}', $sidebarInsert, file_get_contents($sidebar_path)));

                break;
            
            default:
                throw new \InvalidArgumentException('That is not a Dappur addon.');
                break;
        }
        
    }
}
