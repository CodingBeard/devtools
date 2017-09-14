<?php

namespace Codingbeard\Devtools\Message;



class GenerateCollectionMessage
{
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
  protected $entityName;


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
   * @return GenerateCollectionMessage

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
  public function getEntityName()
  {
    return $this->entityName;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param string $entityName
   *
   * @return GenerateCollectionMessage

   */
  public function setEntityName($entityName)
  {
    $this->entityName = $entityName;

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
