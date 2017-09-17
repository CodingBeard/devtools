<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateEventMessage;
use Codingbeard\Devtools\Message\QueueMessageGenerator;
use Codingbeard\Devtools\Processor\GenerateEventProcessor;
use Codingbeard\Devtools\Task\EventGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEventCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:event')
      ->setDescription(
        'devtools generate:event Escaped\\\Namespace group:name:processor messageParams:int like:string this:array'
      )
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the message and processor to create'
      )
      ->addArgument(
        'groupNameProcessor',
        InputArgument::REQUIRED,
        'group:name:processorName of the event'
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
    (new GenerateEventProcessor(CURRENT_DIR))
      ->process(
        (new GenerateEventMessage())
          ->setGroupname($input->getArgument('groupNameProcessor'))
          ->setNamespace($input->getArgument('namespace'))
          ->setProperties($input->getArgument('properties'))
      );
  }
}
