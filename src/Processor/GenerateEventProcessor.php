<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Framework\Module\Processor\ProcessorInterface;
use Codingbeard\Framework\Module\Product\ProductAwareTrait;
use Codingbeard\Framework\Module\Product\ProductInterface;
use Codingbeard\Devtools\Message\GenerateEventMessage;

/**
 * @method ProductInterface getProduct()
 */
class GenerateEventProcessor extends AbstractDevtoolProcessor
  implements ProcessorInterface
{
  use ProductAwareTrait;

  /**
   * @devtoolsOverwritable
   * @return GenerateEventMessage
   */
  public function getMessageClass()
  {
    return new GenerateEventMessage;
  }

  /**
   * @param GenerateEventMessage $message
   *
   * @return bool
   */
  public function process(GenerateEventMessage $message)
  {
    $this->validate($message);

    $groupName = $message->getGroupname();
    $namespace = $message->getNamespace();
    $cliProperties = $message->getProperties();

    if (substr_count($groupName, ':') != 2)
    {
      exit("GroupName should be in the format group:name:processor" . PHP_EOL);
    }

    $eventName = sprintf(
      '%s%s%sAsync%sEvent',
      $this->isCodingbeard ? '' : $this->config->project->name,
      ucfirst(explode(':', $groupName)[0]),
      ucfirst(explode(':', $groupName)[1]),
      ucfirst(explode(':', $groupName)[2])
    );

    $messageName = sprintf(
      '%s%sEventMessage',
      ucfirst(explode(':', $groupName)[0]),
      ucfirst(explode(':', $groupName)[1])
    );

    $properties = [];
    $uses = [];

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

    $abstractNamespace = $this->config->abstract->event;
    $abstractClass = end(explode('\\', $abstractNamespace));

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Processor/Event');

    $generator->generateFile(
      $messageName . '.php',
      'Message',
      'eventMessage',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $messageName,
          'properties' => $properties,
          'identifier' => [
            'group' => explode(':', $groupName)[0],
            'name' => explode(':', $groupName)[1],
          ],
        ],
      ]
    );

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Processor/Event');

    $generator->generateFile(
      $eventName . '.php',
      'Task',
      'event',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $messageName,
          'properties' => $properties,
          'identifier' => [
            'group' => explode(':', $groupName)[0],
            'name'  => explode(':', $groupName)[1],
            'processor' => explode(':', $groupName)[2],
          ],
        ],
        'event'   => [
          'namespace' => $namespace,
          'uses'      => $uses,
          'name'      => $eventName,
          'abstract'  => [
            'class'     => $abstractClass,
            'namespace' => $abstractNamespace,
          ],
        ],
      ]
    );

    echo
      "Don't forget to add the event to the config, AND add the task to the product"
      . PHP_EOL
      . "Config will look like:" . PHP_EOL
      . sprintf(
        'codingbeard
  event:
    product:
      %s:
        %s:
          enabled: true
          legacy: false
  cli:
    task:
      event:
        %s:
          %s:
            description: <do not forget me>
            retryAfterMinutes: 1
            maxWorkerLimit: 5
            batchAmount: 50
',
        explode(':', $groupName)[0],
        explode(':', $groupName)[1],
        explode(':', $groupName)[0],
        explode(':', $groupName)[1]
      ) . PHP_EOL;

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateEventMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateEventMessage
   */
  public function validate(GenerateEventMessage $message)
  {
    if (empty($message->getGroupname()))
    {
      throw new RequiredValueMissingException('groupname');
    }

    if (empty($message->getNamespace()))
    {
      throw new RequiredValueMissingException('namespace');
    }

    if (empty($message->getProperties()))
    {
      throw new RequiredValueMissingException('properties');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));

    return $message;
  }

}
