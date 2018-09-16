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

class TwigEx extends Command
{

    protected function configure()
    {
        $this->setName('twigex')
            ->setDescription('Create a new Twig Extension')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of TwigExtension in CamelCaseFormat.')
            ->setHelp('Creates a new Dappur TwigExtension');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $className = $input->getArgument('name');
        $separated = explode("\\", $className);
        foreach ($separated as $value) {
            if (!CliUtils::isCamelCase($value)) {
                throw new \InvalidArgumentException('Twig Extension names needs to be CamelCaseFormat.');
            }
        }

        if (CliUtils::isDappur()) {
            $class = getcwd() . '/app/src/TwigExtension/'.$className.'.php';
            $base = getcwd() . '/app/src/TwigExtension/';
            $namespace = 'Dappur\TwigExtension';
            $classFileName = "";

            if (file_exists($class)) {
                throw new \InvalidArgumentException('That twig extension already exists.');
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

            # Uses
            $phpClass->addUses(new UseClassModel('Psr\Http\Message\ServerRequestInterface as Request'));
            $phpClass->addUses(new UseClassModel('Psr\Http\Message\ResponseInterface as Response'));

            #Class
            $name = new ClassNameModel($classFileName, '\\Twig_Extension');
            $phpClass->setName($name);

            # Sample Method
            $defaultMethod = new MethodModel('__construct', 'public');
            $defaultMethod->addArgument(new ArgumentModel('request', 'Request'));
            $defaultMethod->addArgument(new ArgumentModel('response', 'Response'));
            $defaultMethod->setBody("\$this->request = \$request;\n        \$this->response = \$response;");
            $phpClass->addMethod($defaultMethod);

            # Extension Name
            $extName = lcfirst($classFileName);
            $getName = new MethodModel('getName', 'public');
            $getName->setBody("return '$extName';");
            $phpClass->addMethod($getName);

            # Twig Functions
            $getFunctions = new MethodModel('getFunctions', 'public');
            $getFunctions->setBody("return [new \Twig_SimpleFunction('$extName', [\$this, '$extName'])];");
            $phpClass->addMethod($getFunctions);

            # Sample Function
            $sample = new MethodModel($extName, 'public');
            $defaultMethod->addArgument(new ArgumentModel('var'));
            $sample->setBody("return \$var;");
            $phpClass->addMethod($sample);

            # Render and write file
            $create = file_put_contents($class, $phpClass->render());

            $output->writeln($className . " class successfully added.");
        }
        
    }


}
