<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateSingleMessage;
use VPN\Module\Processor\AbstractVpnProcessor;

class GenerateSingleProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   * @return GenerateSingleMessage
   */
  public function getMessageClass()
  {
    return new GenerateSingleMessage;
  }

  /**
   * @param GenerateSingleMessage $message
   *
   * @return bool
   */
  public function process(GenerateSingleMessage $message)
  {
    $this->validate($message);

    $taskName = $message->getTaskName() . 'SingleTask';

    $generator = new DevtoolTemplateGenerator(
      $this->parentDir . '/Processor/Single'
    );

    $generator->generateFile(
      $taskName . '.php',
      'Task',
      'single',
      [
        'single' => [
          'namespace'  => $message->getNamespace(),
          'name'       => $taskName,
          'identifier' => [

            'group' => explode(':', $message->getGroupName())[0],
            'name'  => explode(':', $message->getGroupName())[1],
          ],
          'abstract'   => [
            'class'     => end(explode('\\', $this->config->abstract->single)),
            'namespace' => $this->config->abstract->single,
          ],
        ],
      ]
    );

    echo "Don't forget to add the task to the product" . PHP_EOL;

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateSingleMessage $message
   *
   * @throws \Exception
   * @returns GenerateSingleMessage
   */
  public function validate(GenerateSingleMessage $message)
  {
    if (empty($message->getGroupName()))
    {
      throw new \Exception('groupName');
    }

    if (empty($message->getNamespace()))
    {
      throw new \Exception('namespace');
    }

    if (empty($message->getTaskName()))
    {
      throw new \Exception('taskName');
    }

    return $message;
  }

}
