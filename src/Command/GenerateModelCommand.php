<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateModelMessage;
use Codingbeard\Devtools\Processor\GenerateModelProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateModelCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:model')
      ->setDescription('devtools generate:model Escaped\\\Namespace TableName ModelName unique:columns:int plural:like:string unique:this:int')
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the model to create'
      )
      ->addArgument(
        'tableName',
        InputArgument::REQUIRED,
        'Name of table the model is for'
      )
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of model to create'
      )
      ->addArgument(
        'properties',
        InputArgument::IS_ARRAY,
        'Properties of entity in name:type:[unique|plural] format'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // Assuming that the last part of the namespace is the repository name
    $namespaces = explode('\\', $input->getArgument('namespace'));
    $repositoryName = end($namespaces);

    // Assuming that the Module's name is the second to last
    $moduleName = str_replace('Module', '', $namespaces[count($namespaces) - 2]);

    (new GenerateModelProcessor(CURRENT_DIR))
      ->process(
        (new GenerateModelMessage())
          ->setModuleNamespace($input->getArgument('namespace'))
          ->setModuleName($moduleName)
          ->setRepositoryName($repositoryName)
          ->setTableName($input->getArgument('tableName'))
          ->setModelName($input->getArgument('name'))
          ->setProperties($input->getArgument('properties'))
      );
  }
}
