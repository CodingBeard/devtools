<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateWorkerMessage;
use Codingbeard\Devtools\Processor\GenerateWorkerProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateWorkerCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:worker')
      ->setDescription(
        'devtools generate:worker group:name Escaped\\\Namespace NameWithoutMessageOrProcessor messageParams:int like:string this:array'
      )
      ->addArgument(
        'groupName',
        InputArgument::REQUIRED,
        'group:name of the event'
      )
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the message and processor to create'
      )
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of the message and processor to create (Without the Message/Processor on the end)'
      )
      ->addArgument(
        'properties',
        InputArgument::IS_ARRAY,
        'Properties of entity in name:type format'
      );
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateWorkerProcessor(CURRENT_DIR))
      ->process(
        (new GenerateWorkerMessage())
          ->setGroupname($input->getArgument('groupName'))
          ->setNamespace($input->getArgument('namespace'))
          ->setWorkerName($input->getArgument('name'))
          ->setProperties($input->getArgument('properties'))
      );
  }
}
