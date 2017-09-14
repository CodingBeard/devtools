<?php

namespace Codingbeard\Devtools\Message;

use Codingbeard\Framework\Module\Message\AbstractMessage;

class GenerateMigrationMessage extends AbstractMessage
{
  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $databaseName;

  /**
   * @devtoolsOverwritable
   *
   * @var string
   */
  protected $tableName;

  /**
   * @devtoolsOverwritable
   *
   * @var array
   */
  protected $oldColumns;

  /**
   * @devtoolsOverwritable
   *
   * @var array
   */
  protected $newColumns;


  /**
   * @devtoolsOverwritable
   *
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
   * @return GenerateMigrationMessage
   */
  public function setDatabaseName($databaseName)
  {
    $this->databaseName = $databaseName;

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
   * @return GenerateMigrationMessage
   */
  public function setTableName($tableName)
  {
    $this->tableName = $tableName;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return array
   */
  public function getOldColumns()
  {
    return $this->oldColumns;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param array $oldColumns
   *
   * @return GenerateMigrationMessage
   */
  public function setOldColumns($oldColumns)
  {
    $this->oldColumns = $oldColumns;

    return $this;
  }

  /**
   * @devtoolsOverwritable
   *
   * @return array
   */
  public function getNewColumns()
  {
    return $this->newColumns;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param array $newColumns
   *
   * @return GenerateMigrationMessage
   */
  public function setNewColumns($newColumns)
  {
    $this->newColumns = $newColumns;

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
