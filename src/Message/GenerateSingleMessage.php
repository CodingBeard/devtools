<?php

namespace Codingbeard\Devtools\Message;



class GenerateSingleMessage
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
   * @return GenerateSingleMessage
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
   * @return GenerateSingleMessage
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
   * @return GenerateSingleMessage
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;

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
