<?php

namespace Codingbeard\Devtools\Message;



class GenerateRepositoryMessage
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
  protected $repoName;

  /**
   * @devtoolsOverwritable
   *
   * @var array
   */
  protected $columns;

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
  protected $modelNamespace;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $modelName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $entityNamespace;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $entityName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $collectionNamespace;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $collectionName;


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
   * @return GenerateRepositoryMessage
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
  public function getRepoName()
  {
    return $this->repoName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $repoName
   *
   * @return GenerateRepositoryMessage
   */
  public function setRepoName($repoName)
  {
    $this->repoName = $repoName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return array
   */
  public function getColumns()
  {
    return $this->columns;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param array $columns
   *
   * @return GenerateRepositoryMessage
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;

    return $this;
  }

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
   * @return GenerateRepositoryMessage
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
   * @return GenerateRepositoryMessage
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
  public function getModelNamespace()
  {
    return $this->modelNamespace;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $modelNamespace
   *
   * @return GenerateRepositoryMessage
   */
  public function setModelNamespace($modelNamespace)
  {
    $this->modelNamespace = $modelNamespace;

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
   * @return GenerateRepositoryMessage
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getEntityNamespace()
  {
    return $this->entityNamespace;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $entityNamespace
   *
   * @return GenerateRepositoryMessage
   */
  public function setEntityNamespace($entityNamespace)
  {
    $this->entityNamespace = $entityNamespace;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getEntityName()
  {
    return $this->entityName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $entityName
   *
   * @return GenerateRepositoryMessage
   */
  public function setEntityName($entityName)
  {
    $this->entityName = $entityName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getCollectionNamespace()
  {
    return $this->collectionNamespace;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $collectionNamespace
   *
   * @return GenerateRepositoryMessage
   */
  public function setCollectionNamespace($collectionNamespace)
  {
    $this->collectionNamespace = $collectionNamespace;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return string
   */
  public function getCollectionName()
  {
    return $this->collectionName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $collectionName
   *
   * @return GenerateRepositoryMessage
   */
  public function setCollectionName($collectionName)
  {
    $this->collectionName = $collectionName;

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
