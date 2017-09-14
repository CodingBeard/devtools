<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateEntityMessage;
use Codingbeard\Devtools\Message\GenerateEnumMessage;
use Codingbeard\Devtools\Processor\GenerateEntityProcessor;
use Codingbeard\Devtools\Processor\GenerateEnumProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEnumCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:enum')
      ->setDescription(
        'devtools generate:enum Escaped\\\Namespace EnumName CONSTANTS:1 LIKE:2 "THIS:Pending Order"'
      )
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the enum to create'
      )
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of the enum to create'
      )
      ->addArgument(
        'constants',
        InputArgument::IS_ARRAY,
        'Constants of enum in name:value format'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateEnumProcessor(CURRENT_DIR))
      ->process(
        (new GenerateEnumMessage())
          ->setNamespace($input->getArgument('namespace'))
          ->setEnumName($input->getArgument('name'))
          ->setConstants($input->getArgument('constants'))
      );
  }
}
