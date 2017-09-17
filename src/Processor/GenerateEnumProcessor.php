<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateEnumMessage;
use VPN\Module\Processor\AbstractVpnProcessor;

class GenerateEnumProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   * @return GenerateEnumMessage
   */
  public function getMessageClass()
  {
    return new GenerateEnumMessage;
  }

  /**
   * @param GenerateEnumMessage $message
   *
   * @return bool
   */
  public function process(GenerateEnumMessage $message)
  {
    $this->validate($message);

    $name = $message->getEnumName();

    if (substr($name, -4) != 'Enum')
    {
      $name .= 'Enum';
    }

    $constants = [];

    foreach ($message->getConstants() as $constant)
    {
      if (stripos($constant, ':') === false)
      {
        exit("All params after enum name should be in format 'name:value' (don't forget to quote the entire thing if your value has spaces)" . PHP_EOL);
      }

      $constants[] = [
        'name' => strtoupper(explode(':', $constant)[0]),
        'value' => explode(':', $constant)[1]
      ];
    }

    $generator = new DevtoolTemplateGenerator($this->parentDir);

    $generator->generateFile(
      $name . '.php',
      'Enum',
      'enum',
      [
        'enum' => [
          'namespace' => $message->getNamespace(),
          'name'      => $name,
          'constants' => $constants,
        ],
      ]
    );

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateEnumMessage $message
   *
   * @throws \Exception
   * @returns GenerateEnumMessage
   */
  public function validate(GenerateEnumMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new \Exception('namespace');
    }

    if (empty($message->getEnumName()))
    {
      throw new \Exception('enumName');
    }

    if (empty($message->getConstants()))
    {
      throw new \Exception('constants');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));

    return $message;
  }

}
