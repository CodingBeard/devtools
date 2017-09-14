<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateCronMessage;
use Codingbeard\Devtools\Processor\GenerateCronProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCronCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:cron')
      ->setDescription(
        'devtools generate:cron taskGroup:taskName Escaped\\\Namespace NameOfCronTask "* * * * *"'
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
        'schedule',
        InputArgument::REQUIRED,
        'Crontab formatted schedule for the cron to run at'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateCronProcessor(CURRENT_DIR))
      ->process(
        (new GenerateCronMessage())
          ->setGroupName($input->getArgument('groupName'))
          ->setNamespace($input->getArgument('namespace'))
          ->setTaskName($input->getArgument('name'))
          ->setSchedule($input->getArgument('schedule'))
      );
  }
}
