<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateIntervalMessage extends AbstractMessage
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
   * @var int
   */
  protected $interval;


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
   * @return GenerateIntervalMessage
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
   * @return GenerateIntervalMessage
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
   * @return GenerateIntervalMessage
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return int
   */
  public function getInterval()
  {
    return $this->interval;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param int $interval
   *
   * @return GenerateIntervalMessage
   */
  public function setInterval($interval)
  {
    $this->interval = $interval;

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
