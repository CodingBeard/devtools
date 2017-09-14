<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateIntervalMessage;
use VPN\Module\Processor\AbstractVpnProcessor;

class GenerateIntervalProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateIntervalMessage
   */
  public function getMessageClass()
  {
    return new GenerateIntervalMessage;
  }

  /**
   * @param GenerateIntervalMessage $message
   *
   * @return bool
   */
  public function process(GenerateIntervalMessage $message)
  {
    $this->validate($message);

    $taskName = $message->getTaskName() . 'IntervalTask';

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Processor/Interval');

    $generator->generateFile(
      $taskName . '.php',
      'Task',
      'interval',
      [
        'interval'   => [
          'namespace' => $message->getNamespace(),
          'name'      => $taskName,
          'identifier' => [

            'group' => explode(':', $message->getGroupName())[0],
            'name'  => explode(':', $message->getGroupName())[1],
          ],
          'abstract'  => [
            'class'     => end(explode('\\', $this->config->abstract->interval)),
            'namespace' => $this->config->abstract->interval,
          ],
        ],
      ]
    );

    echo
      "Don't forget to add the interval to the config, AND add the task to the product"
      . PHP_EOL
      . "Config will look like:" . PHP_EOL
      . sprintf(
        'codingbeard
  cli:
    task:
      interval:
        %s:
          %s:
            runEverySeconds: %s
            description: <do not forget me>
',
        explode(':', $message->getGroupName())[0],
        explode(':', $message->getGroupName())[1],
        $message->getInterval()
      ) . PHP_EOL;

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateIntervalMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateIntervalMessage
   */
  public function validate(GenerateIntervalMessage $message)
  {
    if (empty($message->getGroupName()))
    {
      throw new RequiredValueMissingException('groupName');
    }

    if (empty($message->getNamespace()))
    {
      throw new RequiredValueMissingException('namespace');
    }

    if (empty($message->getTaskName()))
    {
      throw new RequiredValueMissingException('taskName');
    }

    if (empty($message->getInterval()))
    {
      throw new RequiredValueMissingException('interval');
    }


    return $message;
  }

}
