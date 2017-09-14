<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateEntityMessage;
use Codingbeard\Devtools\Processor\GenerateEntityProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:entity')
      ->setDescription(
        'devtools generate:entity Escaped\\\Namespace EntityName params:int like:string this:array'
      )
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the entity to create'
      )
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of the entity to create'
      )
      ->addArgument(
        'properties',
        InputArgument::IS_ARRAY,
        'Properties of entity in name:type format'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateEntityProcessor(CURRENT_DIR))
      ->process(
        (new GenerateEntityMessage())
          ->setNamespace($input->getArgument('namespace'))
          ->setEntityName($input->getArgument('name'))
          ->setProperties($input->getArgument('properties'))
      );
  }
}
