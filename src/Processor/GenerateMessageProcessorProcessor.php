<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Framework\Module\Processor\ProcessorInterface;
use Codingbeard\Framework\Module\Product\ProductAwareTrait;
use Codingbeard\Framework\Module\Product\ProductInterface;
use Codingbeard\Devtools\Message\GenerateMessageProcessorMessage;

/**
 * @method ProductInterface getProduct()
 */
class GenerateMessageProcessorProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateMessageProcessorMessage
   */
  public function getMessageClass()
  {
    return new GenerateMessageProcessorMessage;
  }

  /**
   * @param GenerateMessageProcessorMessage $message
   *
   * @return bool
   */
  public function process(GenerateMessageProcessorMessage $message)
  {
    $this->validate($message);

    $namespace = $message->getNamespace();
    $messageName = $message->getProcessorName();
    $processorName = $message->getProcessorName();
    $cliProperties = $message->getProperties();

    if (substr(strtolower($messageName), -7) != 'Message')
    {
      $messageName .= 'Message';
    }
    if (substr(strtolower($processorName), -9) != 'Processor')
    {
      $processorName .= 'Processor';
    }

    $uses = [];
    $properties = [];

    foreach ($cliProperties as $cliProperty)
    {
      if (stripos($cliProperty, ':') === false)
      {
        exit("All params after entity name should be in format name:type" . PHP_EOL);
      }

      $property = new \stdClass();
      $property->name = explode(':', $cliProperty)[0];
      $property->type = explode(':', $cliProperty)[1];

      $properties[] = $property;

      if ($property->type == 'Carbon')
      {
        $uses[] = 'Carbon\\Carbon';
      }
      elseif (substr($property->type, -4) == 'Enum')
      {
        $uses[] = $namespace . '\\' . $property->type;
      }
    }

    $abstractNamespace = $this->config->abstract->processor;
    $abstractClass = end(explode('\\', $abstractNamespace));

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Message');

    $generator->generateFile(
      $messageName . '.php',
      'Message',
      'message',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $messageName,
          'properties' => $properties,
        ],
      ]
    );

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Processor');

    $generator->generateFile(
      $processorName . '.php',
      'Processor',
      'processor',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $messageName,
          'properties' => $properties,
        ],
        'processor' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $processorName,
          'abstract' => [
            'class' => $abstractClass,
            'namespace' => $abstractNamespace
          ]
        ],
      ]
    );
    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateMessageProcessorMessage $message
   *
   * @throws \Exception
   * @returns GenerateMessageProcessorMessage
   */
  public function validate(GenerateMessageProcessorMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new \Exception('namespace');
    }

    if (empty($message->getProcessorName()))
    {
      throw new \Exception('processorName');
    }

    if (empty($message->getProperties()))
    {
      throw new \Exception('properties');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));


    return $message;
  }

}
