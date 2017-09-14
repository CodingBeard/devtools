<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateWorkerMessage;

class GenerateWorkerProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateWorkerMessage
   */
  public function getMessageClass()
  {
    return new GenerateWorkerMessage;
  }

  /**
   * @param GenerateWorkerMessage $message
   *
   * @return bool
   */
  public function process(GenerateWorkerMessage $message)
  {
    $this->validate($message);
    $groupName = $message->getGroupname();
    $namespace = $message->getNamespace();
    $messageName = $message->getWorkerName();
    $workerName = $message->getWorkerName();
    $cliProperties = $message->getProperties();

    if (stripos($groupName, ':') === false)
    {
      exit("GroupName should be in the format group:name" . PHP_EOL);
    }

    if (substr(strtolower($messageName), -7) != 'Message')
    {
      $messageName .= 'Message';
    }
    if (substr(strtolower($workerName), -6) != 'Worker')
    {
      $workerName .= 'Worker';
    }

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

    $abstractNamespace = $this->config->abstract->worker;
    $abstractClass = end(explode('\\', $abstractNamespace));

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Message');

    $generator->generateFile(
      $messageName . '.php',
      'Message',
      'workerMessage',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $messageName,
          'properties' => $properties,
          'identifier' => [
            'group' => explode(':', $groupName)[0],
            'name'  => explode(':', $groupName)[1],
          ],
        ],
      ]
    );

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Task/Worker');

    $generator->generateFile(
      $workerName . '.php',
      'Task',
      'worker',
      [
        'message' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $messageName,
          'properties' => $properties,
          'identifier' => [
            'group' => explode(':', $groupName)[0],
            'name'  => explode(':', $groupName)[1],
          ],
        ],
        'worker'   => [
          'namespace' => $namespace,
          'uses'      => $uses,
          'name'      => $workerName,
          'abstract'  => [
            'class'     => $abstractClass,
            'namespace' => $abstractNamespace,
          ],
        ],
      ]
    );

    echo
      "Don't forget to add the worker to the config, AND add the task to the product"
      . PHP_EOL
      . "Config will look like:" . PHP_EOL
      . sprintf(
        'codingbeard
  cli:
    task:
      worker:
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
   * @param GenerateWorkerMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateWorkerMessage
   */
  public function validate(GenerateWorkerMessage $message)
  {
    if (empty($message->getGroupname()))
    {
      throw new RequiredValueMissingException('groupname');
    }

    if (empty($message->getNamespace()))
    {
      throw new RequiredValueMissingException('namespace');
    }

    if (empty($message->getWorkerName()))
    {
      throw new RequiredValueMissingException('workerName');
    }

    if (empty($message->getProperties()))
    {
      throw new RequiredValueMissingException('properties');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));

    return $message;
  }

}
