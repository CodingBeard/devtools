<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateAbstractModelMessage extends AbstractMessage
{
  /**
   * @devtoolsOverwritable
   * @var string
   */
  protected $modelName;

  /**
   * @devtoolsOverwritable
   * @var string
   */
  protected $moduleNamespace;

  /**
   * @devtoolsOverwritable
   * @var string
   */
  protected $databaseName;

  /**
   * @devtoolsOverwritable
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $modelName
   *
   * @return GenerateAbstractModelMessage
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   * @return string
   */
  public function getModuleNamespace()
  {
    return $this->moduleNamespace;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $moduleNamespace
   *
   * @return GenerateAbstractModelMessage
   */
  public function setModuleNamespace($moduleNamespace)
  {
    $this->moduleNamespace = $moduleNamespace;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   * @return string
   */
  public function getDatabaseName()
  {
    return $this->databaseName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $databaseName
   *
   * @return GenerateAbstractModelMessage
   */
  public function setDatabaseName($databaseName)
  {
    $this->databaseName = $databaseName;

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
