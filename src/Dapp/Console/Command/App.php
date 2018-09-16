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

class App extends Command
{

    protected function configure()
    {
        $this->setName('app')
            ->setDescription('Create a new App class')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of App class in CamelCaseFormat.')
            ->setHelp('Creates a new Dappur App class');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $className = $input->getArgument('name');
        $separated = explode("\\", $className);
        foreach ($separated as $value) {
            if (!CliUtils::isCamelCase($value)) {
                throw new \InvalidArgumentException('App class names needs to be CamelCaseFormat.');
            }
        }

        if (CliUtils::isDappur()) {
            $class = getcwd() . '/app/src/App/'.$className.'.php';
            $base = getcwd() . '/app/src/App/';
            $namespace = 'Dappur\App';
            $classFileName = "";

            if (file_exists($class)) {
                throw new \InvalidArgumentException('That controller already exists.');
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
            $name = new ClassNameModel($classFileName, 'App');
            $phpClass->setName($name);

            # Render and write file
            $create = file_put_contents($class, $phpClass->render());

            $output->writeln($className . " class successfully added.");
        }
        
    }


}
