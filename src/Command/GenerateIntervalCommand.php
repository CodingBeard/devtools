<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateIntervalMessage;
use Codingbeard\Devtools\Processor\GenerateIntervalProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateIntervalCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:interval')
      ->setDescription(
        'devtools generate:interval taskGroup:taskName Escaped\\\Namespace NameOfIntervalTask 60'
      )
      ->addArgument(
        'groupName',
        InputArgument::REQUIRED,
        'group:name of the single task'
      )
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the single task'
      )
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of the single task'
      )
      ->addArgument(
        'interval',
        InputArgument::REQUIRED,
        'Interval between executions (Seconds)'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateIntervalProcessor(CURRENT_DIR))
      ->process(
        (new GenerateIntervalMessage())
          ->setGroupName($input->getArgument('groupName'))
          ->setNamespace($input->getArgument('namespace'))
          ->setTaskName($input->getArgument('name'))
          ->setInterval($input->getArgument('interval'))
      );
  }
}
