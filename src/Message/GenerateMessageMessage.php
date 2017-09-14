<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateMessageMessage extends AbstractMessage
{
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
  public function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $namespace
   *
   * @return GenerateMessageMessage

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
   * @return GenerateMessageMessage

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
   * @return GenerateMessageMessage

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
