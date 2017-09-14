<?php

namespace Codingbeard\Devtools\Command;

use Codingbeard\Devtools\Message\GenerateCollectionMessage;
use Codingbeard\Devtools\Processor\GenerateCollectionProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCollectionCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:collection')
      ->setDescription(
        'devtools generate:collection Escaped\\\Namespace EntityName'
      )
      ->addArgument(
        'namespace',
        InputArgument::REQUIRED,
        'Namespace of the collection to create'
      )
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of entity to create'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    (new GenerateCollectionProcessor(CURRENT_DIR))
      ->process(
        (new GenerateCollectionMessage())
        ->setCollectionNamespace($input->getArgument('namespace'))
        ->setEntityName($input->getArgument('name'))
      );
  }
}
