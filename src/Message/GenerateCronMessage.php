<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateCronMessage extends AbstractMessage
{
  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $groupName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $namespace;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $taskName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $schedule;


  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getGroupName()
  {
    return $this->groupName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $groupName
   *
   * @return GenerateCronMessage
   */
  public function setGroupName($groupName)
  {
    $this->groupName = $groupName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $namespace
   *
   * @return GenerateCronMessage
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getTaskName()
  {
    return $this->taskName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $taskName
   *
   * @return GenerateCronMessage
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getSchedule()
  {
    return $this->schedule;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $schedule
   *
   * @return GenerateCronMessage
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;

    return $this;
  }


  /**
   * @devtoolsOverwritable
   */
  public function preSerialize()
  {
  }

  /**
   * @devtoolsOverwritable
   */
  public function postUnserialize()
  {
  }
}
