<?php

namespace Codingbeard\Devtools\Message;



class GenerateEventMessageMessage
{
  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $groupname;

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
  protected $messageName;

  /**
   * @devtoolsOverwritable
   *
   * @var array
   */
  protected $properties;


  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getGroupname()
  {
    return $this->groupname;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $groupname
   *
   * @return GenerateEventMessageMessage

   */
  public function setGroupname($groupname)
  {
    $this->groupname = $groupname;

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
   * @return GenerateEventMessageMessage

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
  public function getMessageName()
  {
    return $this->messageName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $messageName
   *
   * @return GenerateEventMessageMessage

   */
  public function setMessageName($messageName)
  {
    $this->messageName = $messageName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return array
   */
  public function getProperties()
  {
    return $this->properties;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param array $properties
   *
   * @return GenerateEventMessageMessage

   */
  public function setProperties($properties)
  {
    $this->properties = $properties;

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
