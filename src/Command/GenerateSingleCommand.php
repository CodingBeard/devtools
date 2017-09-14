<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateSingleMessage;
use Codingbeard\Devtools\Processor\GenerateSingleProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSingleCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:single')
      ->setDescription(
        'devtools generate:single taskGroup:taskName Escaped\\\Namespace NameOfSingleTask'
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
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateSingleProcessor(CURRENT_DIR))
      ->process(
        (new GenerateSingleMessage())
          ->setGroupName($input->getArgument('groupName'))
          ->setNamespace($input->getArgument('namespace'))
          ->setTaskName($input->getArgument('name'))
      );
  }
}
