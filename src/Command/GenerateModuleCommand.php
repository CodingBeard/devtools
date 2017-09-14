<?php

namespace Codingbeard\Devtools\Command;

use Phalcon\Config\Adapter\Yaml;
use Codingbeard\Devtools\Message\GenerateModuleMessage;
use Codingbeard\Devtools\Processor\GenerateModuleProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateModuleCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:module')
      ->setDescription('devtools generate:module module/EigVpn/cli/build.yaml')
      ->addArgument(
        'buildFile',
        InputArgument::REQUIRED,
        'Path of the build file'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
//    die('Please see the new specifications for codingbeard modules');
    $buildFile = $input->getArgument('buildFile');

    (new GenerateModuleProcessor($buildFile))
      ->process(
        (new GenerateModuleMessage())
          ->setBuildFile($buildFile)
      );
  }
}
