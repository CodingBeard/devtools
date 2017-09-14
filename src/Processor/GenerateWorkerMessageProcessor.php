<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Framework\Module\Processor\ProcessorInterface;
use Codingbeard\Framework\Module\Product\ProductAwareTrait;
use Codingbeard\Framework\Module\Product\ProductInterface;
use Codingbeard\Devtools\Message\GenerateWorkerMessageMessage;

/**
 * @method ProductInterface getProduct()
 */
class GenerateWorkerMessageProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateWorkerMessageMessage
   */
  public function getMessageClass()
  {
    return new GenerateWorkerMessageMessage;
  }

  /**
   * @param GenerateWorkerMessageMessage $message
   *
   * @return bool
   */
  public function process(GenerateWorkerMessageMessage $message)
  {
    $this->validate($message);

    $groupName = $message->getGroupname();
    $namespace = $message->getNamespace();
    $name = $message->getMessageName();
    $cliProperties = $message->getProperties();

    if (stripos($groupName, ':') === false)
    {
      exit("groupname should be in the format group:name" . PHP_EOL);
    }

    if (substr(strtolower($name), -13) != 'WorkerMessage')
    {
      $name .= 'WorkerMessage';
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
      'workerMessage',
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
   * @param GenerateWorkerMessageMessage $message
   *
   * @throws \Exception
   * @returns GenerateWorkerMessageMessage
   */
  public function validate(GenerateWorkerMessageMessage $message)
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
