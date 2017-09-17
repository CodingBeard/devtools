<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateServiceProviderMessage;
use VPN\Module\Processor\AbstractVpnProcessor;

class GenerateServiceProviderProcessor extends AbstractVpnProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateServiceProviderMessage
   */
  public function getMessageClass()
  {
    return new GenerateServiceProviderMessage;
  }

  /**
   * @param GenerateServiceProviderMessage $message
   *
   * @return bool
   */
  public function process(GenerateServiceProviderMessage $message)
  {
    $this->validate($message);

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateServiceProviderMessage $message
   *
   * @throws \Exception
   * @returns GenerateServiceProviderMessage
   */
  public function validate(GenerateServiceProviderMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new \Exception('namespace');
    }

    if (empty($message->getProviderName()))
    {
      throw new \Exception('providerName');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));


    return $message;
  }

}
