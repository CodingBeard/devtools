<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateWorkerMessageMessage;
use Codingbeard\Devtools\Processor\GenerateWorkerMessageProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMessageWorkerCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:message:worker')
      ->setDescription(
        'devtools generate:message:worker group:name Escaped\\\Namespace MessageName params:int like:string this:array'
      )
      ->addArgument(
        'groupname',
        InputArgument::REQUIRED,
        'colon delimited group and name for the message'
      )
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the message to create'
      )
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of the message to create'
      )
      ->addArgument(
        'properties',
        InputArgument::IS_ARRAY,
        'Properties of message in name:type format'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateWorkerMessageProcessor(CURRENT_DIR))
      ->process(
        (new GenerateWorkerMessageMessage())
          ->setGroupname($input->getArgument('groupname'))
          ->setNamespace($input->getArgument('namespace'))
          ->setMessageName($input->getArgument('name'))
          ->setProperties($input->getArgument('properties'))
      );
  }
}
