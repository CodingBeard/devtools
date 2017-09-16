<?php

namespace Codingbeard\Devtools\Command;

use Phalcon\Config\Adapter\Yaml;
use Codingbeard\Devtools\Message\GenerateServiceMessage;
use Codingbeard\Devtools\Processor\GenerateServiceProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateServiceCommand extends Command
{
  /**
   *
   */
  protected function configure()
  {
    $this
      ->setName('generate:service')
      ->setDescription('devtools generate:service $ServiceCode {$ProviderName}PLANProvider')
      ->addArgument(
        'serviceCode',
        InputArgument::REQUIRED,
        'Name of the service you want to generate'
      )
      ->addArgument(
        'provider',
        InputArgument::OPTIONAL,
        'Name of the provider you want to generate'
      );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $serviceCode = $input->getArgument('serviceCode');
    $provider = $input->getArgument('provider');

    (new GenerateServiceProcessor(CURRENT_DIR))
      ->process(
        (new GenerateServiceMessage())
          ->setServiceCode($serviceCode)
          ->setProviders($provider)
      );
  }
}
