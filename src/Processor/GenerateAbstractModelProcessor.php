<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateAbstractModelMessage;

class GenerateAbstractModelProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   * @return GenerateAbstractModelMessage
   */
  public function getMessageClass()
  {
    return new GenerateAbstractModelMessage;
  }

  /**
   * @param GenerateAbstractModelMessage $message
   *
   * @return bool
   */
  public function process(GenerateAbstractModelMessage $message)
  {
    $this->validate($message);

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Library');

    $namespace = $this->config->abstract->modelServiceEnum;
    $abstractClass = end(explode('\\', $this->config->abstract->modelServiceEnum));

    $generator->generateFile(
      $message->getModelName() . '.php',
      'Model',
      'abstractModel',
      [
        'model' => [
          'namespace' => $message->getModuleNamespace() . '\\Library',
          'name' => $message->getModelName(),
          'database' => $message->getDatabaseName(),
          'abstract' => [
            'modelServiceEnum' => [
              'namespace' => $namespace,
              'class' => $abstractClass
            ]
          ]
        ],
      ]
    );

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateAbstractModelMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateAbstractModelMessage
   */
  public function validate(GenerateAbstractModelMessage $message)
  {
    if (empty($message->getModuleNamespace()))
    {
      throw new RequiredValueMissingException('moduleNamespace');
    }

    if (empty($message->getDatabaseName()))
    {
      throw new RequiredValueMissingException('tableName');
    }

    $message->setModuleNamespace(rtrim($message->getModuleNamespace(), '\\'));

    return $message;
  }

}
