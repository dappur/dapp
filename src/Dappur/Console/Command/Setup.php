<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Dappur\Dappurware\CliUtils;


class Setup extends Command
{
    protected function configure()
    {
        $this->setName('setup')
            ->addOption('full', 'f', InputOption::VALUE_OPTIONAL, 'Would you like to do a full setup?', null)
            ->setDescription('Configure a Dappur project.')
            ->setHelp('Configure a Dappur project');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $settings_dist = realpath(getcwd() . '/app/bootstrap/settings.php.dist');
        $settings_tpl = file_get_contents(__DIR__ . "/../../../../templates/settings.tpl");
        $settings_path = getcwd() . '/app/bootstrap/settings.php';
        $helper = $this->getHelper('question');

        if (file_exists($settings_path)) {
            $confirm_overwrite = new ConfirmationQuestion(
                'This will overwrite your existing settings file.  Do you want to continue?',
                false,
                '/^(y|j)/i'
            );

            if (!$helper->ask($input, $output, $confirm_overwrite)) {
                throw new \RuntimeException(
                    'Project setup cancelled.'
                );
            }
        }


        if (file_exists($settings_dist)) {
            if ($input->getOption('full') !== null) {

                $project_name_question = new Question('Project Name (default: Dappur): ', 'Dappur');
                $project_name_question->setValidator(function ($answer) {
                    if (!is_string($answer) || !preg_match('/^[a-zA-Z0-9 -]+$/i', $answer) || $answer == "") {
                        throw new \RuntimeException(
                            'The project name should be letters, numbers and can contain spaces and hyphens.'
                        );
                    }

                    return $answer;
                });
                $project_name = $helper->ask($input, $output, $project_name_question);

                $framework_name = "Dappur";

                $framework_errors_question = new Question('Enable Framework Debug (y/n default: n): ', false);
                $framework_errors_question->setValidator(function ($answer) {
                    if (!preg_match('/^(y|j)/i', $answer)) {
                        return 'false';
                    }

                    return 'true';
                });
                $framework_errors = $helper->ask($input, $output, $framework_errors_question);

                $db_driver_question = new Question('Database Driver (default: mysql): ', 'mysql');
                $db_driver_question->setValidator(function ($answer) {
                    if ($answer == "") {
                        throw new \RuntimeException(
                            'Please enter a database driver.'
                        );
                    }

                    return $answer;
                });
                $db_driver = $helper->ask($input, $output, $db_driver_question);

                $db_host_question = new Question('Database Host (default: localhost): ', 'localhost');
                $db_host_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a host name.'
                        );
                    }

                    return $answer;
                });
                $db_host = $helper->ask($input, $output, $db_host_question);

                $db_port_question = new Question('Database Port (default: 3306): ', 3306);
                $db_port_question->setValidator(function ($answer) {
                    if ($answer == "") {
                        throw new \RuntimeException(
                            'Please enter a valid port.'
                        );
                    }

                    return $answer;
                });
                $db_port = $helper->ask($input, $output, $db_port_question);

                $db_name_question = new Question('Database Name: ', '');
                $db_name_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a database name.'
                        );
                    }

                    return $answer;
                });
                $db_name = $helper->ask($input, $output, $db_name_question);

                $db_user_question = new Question('Database User: ', '');
                $db_user_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a database username.'
                        );
                    }

                    return $answer;
                });
                $db_user = $helper->ask($input, $output, $db_user_question);

                $db_pass_question = new Question('Database Password: ', '');
                $db_pass_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a database username.'
                        );
                    }

                    return $answer;
                });
                $db_pass = $helper->ask($input, $output, $db_pass_question);

                $db_charset_question = new Question('Database Charset (default: utf8): ', 'utf8');
                $db_charset_question->setValidator(function ($answer) {
                    if ($answer == "") {
                        throw new \RuntimeException(
                            'Please enter a valid charset.'
                        );
                    }

                    return $answer;
                });
                $db_charset = $helper->ask($input, $output, $db_charset_question);

                $db_collation_question = new Question('Database Collation (default: utf8_general_ci): ', 'utf8_general_ci');
                $db_collation_question->setValidator(function ($answer) {
                    if ($answer == "") {
                        throw new \RuntimeException(
                            'Please enter a valid collation.'
                        );
                    }

                    return $answer;
                });
                $db_collation = $helper->ask($input, $output, $db_collation_question);

                $db_prefix_question = new Question('Database Table Prefix: ', '');
                $db_prefix_question->setValidator(function ($answer) {
                    if ($answer != "" && !preg_match("/^[a-z0-9-]+$/", $answer)) {
                        throw new \RuntimeException(
                            'Database prefix must be a valid format.'
                        );
                    }

                    return $answer;
                });
                $db_prefix = $helper->ask($input, $output, $db_prefix_question);                

                $twig_cache_question = new Question('Enable Template Cache (y/n default: n): ', false);
                $twig_cache_question->setValidator(function ($answer) {
                    if (!preg_match('/^(y|j)/i', $answer)) {
                        return 'false';
                    }

                    return '\'/../../storage/cache/twig\'';
                    
                });
                $twig_cache = $helper->ask($input, $output, $twig_cache_question);

                $twig_debug_question = new Question('Enable Template Debug (y/n default: n): ', false);
                $twig_debug_question->setValidator(function ($answer) {
                    if (!preg_match('/^(y|j)/i', $answer)) {
                        return 'false';
                    }

                    return 'true';
                });
                $twig_debug = $helper->ask($input, $output, $twig_debug_question);

                $log_file_name_question = new Question('Log File Name (default: dappur): ', 'dappur');
                $log_file_name_question->setValidator(function ($answer) {
                    if ($answer != "" && !preg_match("/^[a-z0-9-]+$/", $answer)) {
                        throw new \RuntimeException(
                            'Log file name must be a valid format.'
                        );
                    }

                    return $answer;
                });
                $log_file_name = $helper->ask($input, $output, $log_file_name_question);

                $le_token_question = new Question('Log Entries Token (default: disabled): ', false);
                $le_token_question->setValidator(function ($answer) {
                    if ($answer == false) {
                        return 'false';
                    }

                    return "'".$answer."'";
                });
                $le_token = $helper->ask($input, $output, $le_token_question);

                $cloudinary_question = new Question('Enable Cloudinary (y/n default: n): ', false);
                $cloudinary_question->setValidator(function ($answer) {
                    if (!preg_match('/^(y|j)/i', $answer)) {
                        return 'false';
                    }

                    return 'true';
                });
                $cloudinary = $helper->ask($input, $output, $cloudinary_question);

                if ($cloudinary == 'true') {
                    $cl_cloud_name_question = new Question('Cloudinary Cloud Name: ', '');
                    $cl_cloud_name_question->setValidator(function ($answer) {
                        if ($answer == "" || !preg_match("/^[a-z0-9-]+$/", $answer)) {
                            throw new \RuntimeException(
                                'Please enter a valid cloud name.'
                            );
                        }

                        return $answer;
                    });
                    $cl_cloud_name = $helper->ask($input, $output, $cl_cloud_name_question);

                    $cl_api_key_question = new Question('Cloudinary Api Key: ', '');
                    $cl_api_key_question->setValidator(function ($answer) {
                        if ($answer == "" || !preg_match("/^[a-z0-9-]+$/", $answer)) {
                            throw new \RuntimeException(
                                'Please enter a valid api key.'
                            );
                        }

                        return $answer;
                    });
                    $cl_api_key = $helper->ask($input, $output, $cl_api_key_question);

                    $cl_api_secret_question = new Question('Cloudinary Api Key: ', '');
                    $cl_api_secret_question->setValidator(function ($answer) {
                        if ($answer == "" || !preg_match("/^[a-z0-9-]+$/", $answer)) {
                            throw new \RuntimeException(
                                'Please enter a valid api key.'
                            );
                        }

                        return $answer;
                    });
                    $cl_api_secret = $helper->ask($input, $output, $cl_api_secret_question);
                }else{
                    $cl_cloud_name = '';
                    $cl_api_key = '';
                    $cl_api_secret = '';
                }
                

            }else{
                $project_name_question = new Question('Project Name (default: Dappur): ', 'Dappur');
                $project_name_question->setValidator(function ($answer) {
                    if (!is_string($answer) || !preg_match('/^[a-zA-Z0-9 -]+$/i', $answer) || $answer == "") {
                        throw new \RuntimeException(
                            'The project name should be letters, numbers and can contain spaces and hyphens.'
                        );
                    }

                    return $answer;
                });
                $project_name = $helper->ask($input, $output, $project_name_question);
                $framework_name = "Dappur";
                $framework_errors = "false";
                $db_driver = "mysql";
                $db_host_question = new Question('Database Host (default: localhost): ', 'localhost');
                $db_host_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a host name.'
                        );
                    }

                    return $answer;
                });
                $db_host = $helper->ask($input, $output, $db_host_question);
                $db_port = 3306;
                $db_name_question = new Question('Database Name: ', '');
                $db_name_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a database name.'
                        );
                    }

                    return $answer;
                });
                $db_name = $helper->ask($input, $output, $db_name_question);
                $db_user_question = new Question('Database User: ', '');
                $db_user_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a database username.'
                        );
                    }

                    return $answer;
                });
                $db_user = $helper->ask($input, $output, $db_user_question);
                $db_pass_question = new Question('Database Password: ', '');
                $db_pass_question->setValidator(function ($answer) {
                    if (!is_string($answer) || $answer == "") {
                        throw new \RuntimeException(
                            'Please enter a database username.'
                        );
                    }

                    return $answer;
                });
                $db_pass = $helper->ask($input, $output, $db_pass_question);
                $db_charset = "utf8";
                $db_collation = "utf8_general_ci";
                $db_prefix = "";
                $twig_cache = "false";
                $twig_debug = "false";
                $log_file_name = "dappur";
                $le_token = "false";
                $cloudinary = "false";
                $cl_cloud_name = "";
                $cl_api_key = "";
                $cl_api_secret = "";
            }

            if (CliUtils::checkDB($db_host, $db_name, $db_user, $db_pass, $db_port, $driver = $db_driver)) {
                
                touch($settings_path);
                file_put_contents($settings_path,$settings_tpl);

                file_put_contents($settings_path,str_replace('{{FRAMEWORK_NAME}}', $framework_name, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{FRAMEWORK_ERRORS}}', $framework_errors, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_DRIVER}}', $db_driver, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_HOST}}', $db_host, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_PORT}}', $db_port, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_NAME}}', $db_name, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_USER}}', $db_user, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_PASS}}', $db_pass, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_CHARSET}}', $db_charset, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_COLLATION}}', $db_collation, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{DB_PREFIX}}', $db_prefix, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{TWIG_CACHE}}', $twig_cache, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{TWIG_DEBUG}}', $twig_debug, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{PROJECT_NAME}}', $project_name, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{LOG_FILE_NAME}}', $log_file_name, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{LE_TOKEN}}', $le_token, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{CLOUDINARY}}', $cloudinary, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{CL_CLOUD_NAME}}', $cl_cloud_name, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{CL_API_KEY}}', $cl_api_key, file_get_contents($settings_path)));
                file_put_contents($settings_path,str_replace('{{CL_API_SECRET}}', $cl_api_secret, file_get_contents($settings_path)));

                $output->writeln($project_name . " has been successfully set up.");
            }
        }else{
            throw new \InvalidArgumentException('New Dappur project not detected.');
        }

    }
}
