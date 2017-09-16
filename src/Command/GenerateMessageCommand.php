<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateMessageMessage;
use Codingbeard\Devtools\Processor\GenerateMessageProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMessageCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:message')
      ->setDescription(
        'devtools generate:message Escaped\\\Namespace MessageName params:int like:string this:array'
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
    (new GenerateMessageProcessor(CURRENT_DIR))
      ->process(
        (new GenerateMessageMessage())
          ->setNamespace($input->getArgument('namespace'))
          ->setMessageName($input->getArgument('name'))
          ->setProperties($input->getArgument('properties'))
      );
  }
}
