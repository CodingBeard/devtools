<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateMessageProcessorMessage;
use Codingbeard\Devtools\Processor\GenerateMessageProcessorProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProcessorCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:processor')
      ->setDescription(
        'devtools generate:processor Escaped\\\Namespace NameWithoutMessageOrProcessor messageParams:int like:string this:array'
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
    (new GenerateMessageProcessorProcessor(CURRENT_DIR))
      ->process(
        (new GenerateMessageProcessorMessage())
          ->setNamespace($input->getArgument('namespace'))
          ->setProcessorName($input->getArgument('name'))
          ->setProperties($input->getArgument('properties'))
      );
  }
}
