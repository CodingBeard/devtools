<?php

namespace Codingbeard\Devtools\Processor;

class GenerateContainerProcessor extends AbstractDevtoolProcessor
{
  /** @var \stdClass */
  protected $moduleConfig;

  /** @var \stdClass */
  protected $previousConfig;

  /**
   * @devtoolsOverwritable
   * @return GenerateContainerMessage
   */
  public function getMessageClass()
  {
    return new GenerateContainerMessage;
  }


  public function process()
  {
    $repositories = [];
    $previousRepositories = [];

    foreach ($this->moduleConfig->repositories as $repositoryName => $repository)
    {
      $repositoryName = ucfirst($repositoryName) . 'Repository';

      $enums = [];

      if (isset($repository->enums) && is_object($repository->enums))
      {
        foreach ($repository->enums as $enumName => $values)
        {
          $constants = [];

          foreach ($values as $value)
          {
            if (stristr($value, ' '))
            {
              $constants[explode(' ', $value)[0]] = explode(' ', $value)[0] . ':' . implode(' ', array_slice(explode(' ', $value), 1));
            }
            else
            {
              $constants[$value] = $value . ':' . str_replace('_', ' ', $value);
            }
          }

          $enums[$enumName] = (object)[
            'name' => $enumName,
            'constants' => $constants
          ];
        }
      }

      $columns = [];

      foreach ($repository->columns as $columnName => $column)
      {
        $bits = explode(' ', $column);

        $columns[] = (object)[
          'name'       => $columnName,
          'type'       => $bits[1],
          'unique'     => ($bits[0] == 'unique' ? true : false),
          'entityType' => isset($bits[2]) ? $bits[2] : false,
        ];
      }

      $repositoryNamespace = sprintf(
        '%s\\Data\\%s\\%s',
        $namespace,
        str_replace('Repository', '', $repositoryName),
        $repositoryName
      );

      $repositories[$repositoryName] = (object)[
        'name'      => $repositoryName,
        'namespace' => $repositoryNamespace,
        'table'     => $repository->table,
        'columns'   => $columns,
        'enums'     => (object)$enums,
      ];
    }

    if ($this->previousConfig)
    {
      foreach ($this->previousConfig->repositories as $repositoryName => $repository)
      {
        $repositoryName = ucfirst($repositoryName) . 'Repository';

        $columns = [];

        foreach ($repository->columns as $columnName => $column)
        {
          $bits = explode(' ', $column);

          $columns[] = (object)[
            'name'       => $columnName,
            'type'       => $bits[1],
            'unique'     => ($bits[0] == 'unique' ? true : false),
            'entityType' => isset($bits[2]) ? $bits[2] : false,
          ];
        }

        $previousRepositories[$repositoryName] = (object)[
          'table'   => $repository->table,
          'columns' => $columns,
        ];
      }
    }
  }
}
