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
            ->setDescription('Create a new Controller')
            ->addArgument('name', InputArgument::REQUIRED, 'What table would you like to create a model for?')
            ->setHelp('Creates a new Dappur model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $controllerName = $input->getArgument('name');
        $separated = explode("\\", $controllerName);
        foreach ($separated as $value) {
            if (!CliUtils::isCamelCase($value)) {
                throw new \InvalidArgumentException('Controller names needs to be CamelCaseFormat.');
            }
        }

        if (CliUtils::isDappur()) {
            $containerBootstrap = getcwd() . '/app/bootstrap/controllers.php';
            $controller = getcwd() . '/app/src/Controller/'.$controllerName.'.php';
            $base = getcwd() . '/app/src/Controller/';
            $namespace = 'Dappur\Controller';
            $controllerFileName = "";

            if (file_exists($controller)) {
                throw new \InvalidArgumentException('That controller already exists.');
            }

            foreach ($separated as $sep) {
                if ($sep == end($separated)) {
                    touch($controller);
                    $controllerFileName = $sep;
                    continue;
                }
                mkdir($base . "/$sep");
                $base = $base . "/$sep";
                $namespace = $namespace . "\\$sep";
            }
            
            $containerAppend = file_get_contents(__DIR__ . "/../../../../templates/controller-append.tpl");
            $containerAppend = str_replace("{{CONTROLLER_NAME_STRIPPED}}", str_replace("\\", "", $controllerName), $containerAppend);
            $containerAppend = str_replace("{{CONTROLLER_NAME}}", $controllerName, $containerAppend);

            $append = file_put_contents($containerBootstrap, $containerAppend, FILE_APPEND | LOCK_EX);

            $phpClass = new ClassModel();
            # Namespace
            $phpClass->setNamespace(new NamespaceModel($namespace));

            # Uses
            $phpClass->addUses(new UseClassModel('Dappur\Controller\Controller as Controller'));
            $phpClass->addUses(new UseClassModel('Psr\Http\Message\ServerRequestInterface as Request'));
            $phpClass->addUses(new UseClassModel('Psr\Http\Message\ResponseInterface as Response'));

            #Class
            $name = new ClassNameModel($controllerFileName, 'Controller');
            $phpClass->setName($name);

            # Sample Method
            $defaultMethod = new MethodModel('sample', 'public');
            $defaultMethod->addArgument(new ArgumentModel('request', 'Request'));
            $defaultMethod->addArgument(new ArgumentModel('response', 'Response'));
            $defaultMethod->setBody('return $this->view->render($response, \'home.twig\');');
            $phpClass->addMethod($defaultMethod);

            # Render and write file
            $create = file_put_contents($controller, $phpClass->render());

            $output->writeln($controllerName . " controller successfully added.");
        }
        
    }


}
