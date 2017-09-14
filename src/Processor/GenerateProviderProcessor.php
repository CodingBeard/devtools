<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateProviderMessage;
use VPN\Module\Processor\AbstractVpnProcessor;

class GenerateProviderProcessor extends AbstractVpnProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateProviderMessage
   */
  public function getMessageClass()
  {
    return new GenerateProviderMessage;
  }

  /**
   * @param GenerateProviderMessage $message
   *
   * @return bool
   */
  public function process(GenerateProviderMessage $message)
  {
    $this->validate($message);

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateProviderMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateProviderMessage
   */
  public function validate(GenerateProviderMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new RequiredValueMissingException('namespace');
    }

    if (empty($message->getProviderName()))
    {
      throw new RequiredValueMissingException('providerName');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));


    return $message;
  }

}
