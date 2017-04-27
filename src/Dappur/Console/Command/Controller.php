<?php

namespace Dappur\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Dappur\Dappurware\CliUtils;
use Krlove\CodeGenerator\Model\ClassModel;
use Krlove\CodeGenerator\Model\NamespaceModel;
use Krlove\CodeGenerator\Model\UseClassModel;
use Krlove\CodeGenerator\Model\ClassNameModel;
use Krlove\CodeGenerator\Model\MethodModel;
use Krlove\CodeGenerator\Model\ArgumentModel;

class Controller extends Command
{

    protected function configure()
    {
        $this->setName('controller')
            ->setDescription('Create a new Dappur model')
            ->addArgument('controller-name', InputArgument::REQUIRED, 'What table would you like to create a model for?')
            ->setHelp('Creates a new Dappur model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $controllerName = $input->getArgument('controller-name');

        $dappur = CliUtils::isDappur();

        if (!CliUtils::isCamelCase($controllerName)) {
            throw new \InvalidArgumentException('Controller name needs to be CamelCaseFormat.');
        }

        if ($dappur) {
            $containerBootstrap = getcwd() . '/app/bootstrap/controllers.php';
            $controller = getcwd() . '/app/src/Controller/'.$controllerName.'.php';

            if (file_exists($controller)) {
                throw new \InvalidArgumentException('That controller already exists.');
            }else{
                touch($controller);
            }
            $containerAppend = file_get_contents(__DIR__ . "/../../../../templates/controller-append.tpl");

            $append = file_put_contents($containerBootstrap, str_replace("{{CONTROLLER_NAME}}", $controllerName, $containerAppend), FILE_APPEND | LOCK_EX);

            $phpClass = new ClassModel();
            $phpClass->setNamespace(new NamespaceModel('Dappur\Controller'));
            $phpClass->addUses(new UseClassModel('Psr\Http\Message\ServerRequestInterface as Request'));
            $phpClass->addUses(new UseClassModel('Psr\Http\Message\ResponseInterface as Response'));
            $name = new ClassNameModel($controllerName, 'Controller');
            $phpClass->setName($name);
            $defaultMethod = new MethodModel('default', 'public');
            $defaultMethod->addArgument(new ArgumentModel('request', 'Request'));
            $defaultMethod->addArgument(new ArgumentModel('response', 'Response'));
            $defaultMethod->setBody('return $this->view->render($response, \'App/home.twig\');');
            $phpClass->addMethod($defaultMethod);
            $create = file_put_contents($controller, $phpClass->render());

            $output->writeln($controllerName . " controller successfully added.");
        }
        
    }


}
