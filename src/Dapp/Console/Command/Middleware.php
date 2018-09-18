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

class Middleware extends Command
{

    protected function configure()
    {
        $this->setName('middleware')
            ->setDescription('Create a new Middleware')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of Middleware in CamelCaseFormat.')
            ->setHelp('Creates a new Dappur TwigExtension');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $className = $input->getArgument('name');
        $separated = explode("\\", $className);
        foreach ($separated as $value) {
            if (!CliUtils::isCamelCase($value)) {
                throw new \InvalidArgumentException('Middleware names needs to be CamelCaseFormat.');
            }
        }

        $dappur = CliUtils::isDappur();
        $class = getcwd() . '/app/src/Middleware/'.$className.'.php';
        $base = getcwd() . '/app/src/Middleware/';
        $namespace = 'Dappur\Middleware';
        $classFileName = "";

        if (file_exists($class)) {
            throw new \InvalidArgumentException('That middleware already exists.');
        }

        foreach ($separated as $sep) {
            if ($sep == end($separated)) {
                touch($class);
                $classFileName = $sep;
                continue;
            }
            mkdir($base . "/$sep");
            $base = $base . "/$sep";
            $namespace = $namespace . "\\$sep";
        }

        $phpClass = new ClassModel();
        # Namespace
        $phpClass->setNamespace(new NamespaceModel($namespace));

        #Class
        $name = new ClassNameModel($classFileName, 'Middleware');
        $phpClass->setName($name);

        # Method
        $defaultMethod = new MethodModel('__invoke', 'public');
        $defaultMethod->addArgument(new ArgumentModel('request'));
        $defaultMethod->addArgument(new ArgumentModel('response'));
        $defaultMethod->addArgument(new ArgumentModel('next'));
        $defaultMethod->setBody('return $next($request, $response);');
        $phpClass->addMethod($defaultMethod);

        # Render and write file
        $create = file_put_contents($class, $phpClass->render());

        $output->writeln($className . " Middleware class successfully added.");
    }
}
