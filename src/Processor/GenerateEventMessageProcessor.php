<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Framework\Module\Processor\ProcessorInterface;
use Codingbeard\Framework\Module\Product\ProductAwareTrait;
use Codingbeard\Framework\Module\Product\ProductInterface;
use Codingbeard\Devtools\Message\GenerateEventMessageMessage;

/**
 * @method ProductInterface getProduct()
 */
class GenerateEventMessageProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateEventMessageMessage
   */
  public function getMessageClass()
  {
    return new GenerateEventMessageMessage;
  }

  /**
   * @param GenerateEventMessageMessage $message
   *
   * @return bool
   */
  public function process(GenerateEventMessageMessage $message)
  {
    $this->validate($message);

    $groupName = $message->getGroupname();
    $namespace = $message->getNamespace();
    $name = $message->getMessageName();
    $cliProperties = $message->getProperties();

    if (substr(strtolower($name), -12) != 'EventMessage')
    {
      $name .= 'EventMessage';
    }

    if (stripos($groupName, ':') === false)
    {
      exit("groupname should be in the format group:name" . PHP_EOL);
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

      if (in_array($property->name, ['name', 'group']))
      {
        die("Cannot have a parameter with reserved names: 'name', 'group'" . PHP_EOL);
      }

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
      'eventMessage',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $name,
          'identifier' => [
            'group' => explode(':', $groupName)[0],
            'name' => explode(':', $groupName)[1],
          ],
          'properties' => $properties,
        ],
      ]
    );
    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateEventMessageMessage $message
   *
   * @throws \Exception
   * @returns GenerateEventMessageMessage
   */
  public function validate(GenerateEventMessageMessage $message)
  {
    if (empty($message->getGroupname()))
    {
      throw new \Exception('groupname');
    }

    if (empty($message->getNamespace()))
    {
      throw new \Exception('namespace');
    }

    if (empty($message->getMessageName()))
    {
      throw new \Exception('messageName');
    }

    if (empty($message->getProperties()))
    {
      throw new \Exception('properties');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));


    return $message;
  }

}
