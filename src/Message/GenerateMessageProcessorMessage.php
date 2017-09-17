<?php

namespace Codingbeard\Devtools\Message;



class GenerateMessageProcessorMessage
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
  protected $processorName;

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
   * @return GenerateMessageProcessorMessage

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
  public function getProcessorName()
  {
    return $this->processorName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $processorName
   *
   * @return GenerateMessageProcessorMessage

   */
  public function setProcessorName($processorName)
  {
    $this->processorName = $processorName;

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
   * @return GenerateMessageProcessorMessage

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
