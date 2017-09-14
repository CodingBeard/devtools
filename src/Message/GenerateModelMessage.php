<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateModelMessage extends AbstractMessage
{
  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $moduleNamespace;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $moduleName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $repositoryName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $tableName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $modelName;

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
  public function getModuleNamespace()
  {
    return $this->moduleNamespace;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $moduleNamespace
   *
   * @return GenerateModelMessage

   */
  public function setModuleNamespace($moduleNamespace)
  {
    $this->moduleNamespace = $moduleNamespace;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $moduleName
   *
   * @return GenerateModelMessage

   */
  public function setModuleName($moduleName)
  {
    $this->moduleName = $moduleName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getRepositoryName()
  {
    return $this->repositoryName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $repositoryName
   *
   * @return GenerateModelMessage

   */
  public function setRepositoryName($repositoryName)
  {
    $this->repositoryName = $repositoryName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getTableName()
  {
    return $this->tableName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $tableName
   *
   * @return GenerateModelMessage

   */
  public function setTableName($tableName)
  {
    $this->tableName = $tableName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
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
   * @return GenerateModelMessage

   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;

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
   * @return GenerateModelMessage

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
