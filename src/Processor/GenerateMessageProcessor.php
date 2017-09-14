<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Framework\Module\Processor\ProcessorInterface;
use Codingbeard\Framework\Module\Product\ProductAwareTrait;
use Codingbeard\Framework\Module\Product\ProductInterface;
use Codingbeard\Devtools\Message\GenerateMessageMessage;

/**
 * @method ProductInterface getProduct()
 */
class GenerateMessageProcessor extends AbstractDevtoolProcessor implements ProcessorInterface
{
  use ProductAwareTrait;

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateMessageMessage
   */
  public function getMessageClass()
  {
    return new GenerateMessageMessage;
  }

  /**
   * @param GenerateMessageMessage $message
   *
   * @return bool
   */
  public function process(GenerateMessageMessage $message)
  {
    $this->validate($message);

    $namespace = $message->getNamespace();
    $name = $message->getMessageName();
    $cliProperties = $message->getProperties();

    if (substr(strtolower($name), -7) != 'Message')
    {
      $name .= 'Message';
    }

    $uses = [];
    $properties = [];

    foreach ($cliProperties as $cliProperty)
    {
      if (stripos($cliProperty, ':') === false)
      {
        exit("All params after message name should be in format name:type" . PHP_EOL);
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

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Message');

    $generator->generateFile(
      $name . '.php',
      'Message',
      'message',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $name,
          'properties' => $properties,
        ],
      ]
    );
    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateMessageMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateMessageMessage
   */
  public function validate(GenerateMessageMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new RequiredValueMissingException('namespace');
    }

    if (empty($message->getMessageName()))
    {
      throw new RequiredValueMissingException('messageName');
    }

    if (empty($message->getProperties()))
    {
      throw new RequiredValueMissingException('properties');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));


    return $message;
  }

}
