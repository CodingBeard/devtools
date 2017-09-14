<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateCronMessage;
use VPN\Module\Processor\AbstractVpnProcessor;

class GenerateCronProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateCronMessage
   */
  public function getMessageClass()
  {
    return new GenerateCronMessage;
  }

  /**
   * @param GenerateCronMessage $message
   *
   * @return bool
   */
  public function process(GenerateCronMessage $message)
  {
    $this->validate($message);

    $taskName = $message->getTaskName() . 'CronTask';

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/Processor/Cron');

    $generator->generateFile(
      $taskName . '.php',
      'Task',
      'cron',
      [
        'cron'   => [
          'namespace' => $message->getNamespace(),
          'name'      => $taskName,
          'identifier' => [

            'group' => explode(':', $message->getGroupName())[0],
            'name'  => explode(':', $message->getGroupName())[1],
          ],
          'abstract'  => [
            'class'     => end(explode('\\', $this->config->abstract->cron)),
            'namespace' => $this->config->abstract->cron,
          ],
        ],
      ]
    );

    echo
      "Don't forget to add the cron to the config, AND add the task to the product"
      . PHP_EOL
      . "Config will look like:" . PHP_EOL
      . sprintf(
        'codingbeard
  cli:
    task:
      cron:
        %s:
          %s:
            schedule: \'%s\'
            description: <do not forget me>
',
        explode(':', $message->getGroupName())[0],
        explode(':', $message->getGroupName())[1],
        $message->getSchedule()
      ) . PHP_EOL;

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateCronMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateCronMessage
   */
  public function validate(GenerateCronMessage $message)
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

    if (empty($message->getSchedule()))
    {
      throw new RequiredValueMissingException('schedule');
    }


    return $message;
  }

}
