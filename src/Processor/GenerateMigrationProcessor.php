<?php

namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateMigrationMessage;

class GenerateMigrationProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   * @return GenerateMigrationMessage
   */
  public function getMessageClass()
  {
    return new GenerateMigrationMessage;
  }

  /**
   * @param GenerateMigrationMessage $message
   *
   * @return bool
   */
  public function process(GenerateMigrationMessage $message)
  {
    $this->validate($message);

    $change = false;
    $addedColumns = [];
    $deletedColumns = [];

    if (is_array($message->getOldColumns()) && count($message->getOldColumns()))
    {
      foreach ($message->getNewColumns() as $column)
      {
        if (!in_array($column, $message->getOldColumns()))
        {
          $change = true;
          $addedColumns[] = $column;
        }
      }

      foreach ($message->getOldColumns() as $column)
      {
        if (!in_array($column, $message->getNewColumns()))
        {
          $change = true;
          $deletedColumns[] = $column;
        }
      }
    }
    else
    {
      $change = true;
    }

    if (!$change)
    {
      return true;
    }

    if (count($addedColumns) && count($deletedColumns))
    {
      echo PHP_EOL . "You have added and removed at least 1 column at the same time.
Please be aware I'm treating that like adding and dropping a column.
If you wanted to rename a column instead then you'll have to modify the migration!"
      . PHP_EOL . PHP_EOL;
    }

    $newColumns = [];
    $oldColumns = [];

    if (!$message->getOldColumns())
    {
      $name = sprintf(
        'Add%sTableTo%s',
        studly_case($message->getTableName()),
        studly_case($message->getDatabaseName())
      );

      $template = 'newMigration';

      foreach ($message->getNewColumns() as $column)
      {
        $newColumns[] = (object)[
          'name' => explode(':', $column)[0],
          'type' => explode(':', $column)[1],
        ];
      }

      if (is_array($message->getOldColumns()) && count($message->getOldColumns()))
      {
        foreach ($message->getNewColumns() as $column)
        {
          $oldColumns[] = (object)[
            'name' => explode(':', $column)[0],
            'type' => explode(':', $column)[1],
          ];
        }
      }
    }
    else
    {
      $name = sprintf(
        'Update%sTableIn%s',
        studly_case($message->getTableName()),
        studly_case($message->getDatabaseName())
      );

      $template = 'updateMigration';

      foreach ($addedColumns as $key => $column)
      {
        $addedColumns[$key] = (object)[
          'name' => explode(':', $column)[0],
          'type' => explode(':', $column)[1],
        ];
      }

      foreach ($deletedColumns as $key => $column)
      {
        $deletedColumns[$key] = (object)[
          'name' => explode(':', $column)[0],
          'type' => explode(':', $column)[1],
        ];
      }
    }

    $generator = new DevtoolTemplateGenerator($this->parentDir . '/migrations');

    $generator->generateFile(
      date('Y_m_d_His_') . $name . '.php',
      'Migration',
      $template,
      [
        'migration' => [
          'name' => $name,
        ],
        'database'  => [
          'name' => $message->getDatabaseName(),
        ],
        'table'     => [
          'name'    => $message->getTableName(),
          'columns' => [
            'old'     => $oldColumns,
            'new'     => $newColumns,
            'added'   => $addedColumns,
            'deleted' => $deletedColumns,
          ],
        ],
      ]
    );

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateMigrationMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateMigrationMessage
   */
  public function validate(GenerateMigrationMessage $message)
  {
    if (empty($message->getDatabaseName()))
    {
      throw new RequiredValueMissingException('databaseName');
    }

    if (empty($message->getTableName()))
    {
      throw new RequiredValueMissingException('tableName');
    }

    if (empty($message->getNewColumns()))
    {
      throw new RequiredValueMissingException('newColumns');
    }

    return $message;
  }

}
