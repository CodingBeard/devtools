<?php

namespace Codingbeard\Devtools\Message;



class GenerateEnumMessage
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
  protected $enumName;

  /**
   * @devtoolsOverwritable
   *
   * @var array
   */
  protected $constants;


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
   * @return GenerateEnumMessage
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
  public function getEnumName()
  {
    return $this->enumName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $enumName
   *
   * @return GenerateEnumMessage
   */
  public function setEnumName($enumName)
  {
    $this->enumName = $enumName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return array
   */
  public function getConstants()
  {
    return $this->constants;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param array $constants
   *
   * @return GenerateEnumMessage
   */
  public function setConstants($constants)
  {
    $this->constants = $constants;

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
