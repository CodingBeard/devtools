<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Devtools\Message\GenerateAbstractModelMessage;
use Codingbeard\Devtools\Message\GenerateCollectionMessage;
use Codingbeard\Devtools\Message\GenerateEntityMessage;
use Codingbeard\Devtools\Message\GenerateEnumMessage;
use Codingbeard\Devtools\Message\GenerateMigrationMessage;
use Codingbeard\Devtools\Message\GenerateModelMessage;
use Codingbeard\Devtools\Message\GenerateRepositoryMessage;
use Codingbeard\Framework\Module\Processor\Exception\InvalidMessageValueException;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateModuleMessage;

class GenerateModuleProcessor extends AbstractDevtoolProcessor
{
  /** @var \stdClass */
  protected $moduleConfig;

  /** @var \stdClass */
  protected $previousConfig;

  /**
   * @devtoolsOverwritable
   * @return GenerateModuleMessage
   */
  public function getMessageClass()
  {
    return new GenerateModuleMessage;
  }

  /**
   * @param GenerateModuleMessage $message
   *
   * @return bool
   */
  public function process(GenerateModuleMessage $message)
  {
    $this->validate($message);

    /**
     * Module start
     */
    $moduleName = $this->moduleConfig->name . 'Module';
    $moduleClassPrefix = $this->moduleConfig->name;
    $namespace = rtrim($this->moduleConfig->namespace, '\\');
    $moduleDir = dirname($message->getBuildFile()) . '/../';
    if (isset($this->moduleConfig->dir))
    {
      $moduleDir = $this->moduleConfig->dir;
    }

    $abstractClass = end(explode('\\', $this->config->abstract->module));
    $abstractNamespace = $this->config->abstract->module;

    $generator = new DevtoolTemplateGenerator($moduleDir);

    $generator->generateFile(
      $moduleName . '.php',
      'Module',
      'module',
      [
        'module' => [
          'namespace'    => $namespace,
          'name'         => $moduleName,
          'classPrefix'  => $moduleClassPrefix,
          'abstract' => [
            'class' => $abstractClass,
            'namespace' => $abstractNamespace,
          ]
        ],
      ]
    );
    /**
     * Module end
     */

    /**
     * Container start
     */

    $repositories = [];
    $previousRepositories = [];

    foreach ($this->moduleConfig->repositories as $repositoryName => $repository)
    {
      $repositoryName = $moduleClassPrefix . ucfirst($repositoryName) . 'Repository';

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
        str_replace(['Repository', $moduleClassPrefix], '', $repositoryName),
        $repositoryName
      );

      $repositories[$repositoryName] = (object)[
        'name'      => $repositoryName,
        'prefix'    => str_replace(['Repository', $moduleClassPrefix], '', $repositoryName),
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

    $containerName = $moduleClassPrefix . 'DataContainer';
    $abstractClass = end(explode('\\', $this->config->abstract->dataContainer));
    $abstractNamespace = $this->config->abstract->dataContainer;

    $generator = new DevtoolTemplateGenerator($moduleDir . '/Container');

    $generator->generateFile(
      $containerName . '.php',
      'Container',
      'dataContainer',
      [
        'container' => [
          'namespace'    => $namespace . '\\Container',
          'name'         => $containerName,
          'classPrefix'  => $moduleClassPrefix,
          'repositories' => $repositories,
          'abstract'     => [
            'class'     => $abstractClass,
            'namespace' => $abstractNamespace,
          ],
        ],
      ]
    );

    $containerName = $moduleClassPrefix . 'HandlerContainer';
    $abstractClass = end(explode('\\', $this->config->abstract->handlerContainer));
    $abstractNamespace = $this->config->abstract->handlerContainer;

    $generator = new DevtoolTemplateGenerator($moduleDir . '/Container');

    $generator->generateFile(
      $containerName . '.php',
      'Container',
      'handlerContainer',
      [
        'container' => [
          'namespace'    => $namespace . '\\Container',
          'name'         => $containerName,
          'abstract'     => [
            'class'     => $abstractClass,
            'namespace' => $abstractNamespace,
          ],
        ],
      ]
    );

    $containerName = $moduleClassPrefix . 'ProcessorContainer';
    $abstractClass = end(explode('\\', $this->config->abstract->processorContainer));
    $abstractNamespace = $this->config->abstract->processorContainer;

    $generator = new DevtoolTemplateGenerator($moduleDir . '/Container');

    $generator->generateFile(
      $containerName . '.php',
      'Container',
      'processorContainer',
      [
        'container' => [
          'moduleNamespace' => $namespace,
          'namespace'       => $namespace . '\\Container',
          'name'            => $containerName,
          'classPrefix'     => $moduleClassPrefix,
          'abstract'        => [
            'class'     => $abstractClass,
            'namespace' => $abstractNamespace,
          ],
        ],
      ]
    );

    /**
     * Container end
     */

    /**
     * ProcessorSetupHandler start
     */

    $handlerName = $moduleClassPrefix . 'ProcessorSetupHandler';

    $generator = new DevtoolTemplateGenerator($moduleDir . '/Processor');

    $generator->generateFile(
      $handlerName . '.php',
      'Processor',
      'processorSetupHandler',
      [
        'handler' => [
          'namespace'       => $namespace . '\\Processor',
          'name'            => $handlerName,
        ],
      ]
    );

    /**
     * ProcessorSetupHandler end
     */

    if (count($repositories))
    {
      /**
       * Abstract model start
       */
      (new GenerateAbstractModelProcessor($moduleDir))
        ->process(
          (new GenerateAbstractModelMessage())
            ->setModelName('Abstract' . $moduleClassPrefix . 'Model')
            ->setModuleNamespace($namespace)
            ->setDatabaseName($this->moduleConfig->database)
        );
      /**
       * Abstract model end
       */
    }

    /**
     * Model start (Plus migration, entity, enum setups)
     */
    foreach ($repositories as $repository)
    {
      $newMigrationColumns = [];
      $previousMigrationColumns = [];
      $modelColumns = [];
      $entityColumns = [];

      foreach ($repository->columns as $column)
      {
        if (substr($column->entityType, -4) == 'Enum')
        {
          $enumName = $column->entityType;

          if (!isset($repository->enums->$enumName))
          {
            $repository->enums->$enumName = (object)[
              'name' => $enumName,
              'constants' => [
                'CONSTANT_NAME:value'
              ]
            ];
          }
        }

        $newMigrationColumns[] = sprintf(
          '%s:%s',
          $column->name,
          $column->type
        );

        if ($column->type == 'datetime')
        {
          $column->type = 'string';
          $column->entityType = 'Carbon';
        }

        if ($column->type == 'text')
        {
          $column->type = 'string';
        }

        $modelColumns[] = sprintf(
          '%s:%s:%s',
          $column->name,
          $column->type,
          ($column->unique ? 'unique' : 'plural')
        );

        $entityColumns[] = sprintf(
          '%s:%s',
          $column->name,
          ($column->entityType ? $column->entityType : $column->type)
        );
      }

      if (isset($previousRepositories[$repository->name]))
      {
        foreach ($previousRepositories[$repository->name]->columns as $column)
        {
          $previousMigrationColumns[] = sprintf(
            '%s:%s',
            $column->name,
            $column->type
          );
        }
      }

      $repositoryName = str_replace('Repository', '', $repository->name);
      $moduleName = str_replace('Module', '', $moduleName);

      $repositoryDir = sprintf(
        '%s/Data/%s',
        $moduleDir,
        str_replace($moduleName, '', $repositoryName)
      );

      $repositoryNamespace = sprintf(
        '%s\\Data\\%s',
        $namespace,
        str_replace($moduleName, '', $repositoryName)
      );

      (new GenerateModelProcessor($repositoryDir))
        ->process(
          (new GenerateModelMessage())
            ->setModuleName($moduleName)
            ->setModuleNamespace($namespace)
            ->setRepositoryName($repositoryName)
            ->setModelName($repositoryName)
            ->setProperties($modelColumns)
            ->setTableName($repository->table)
        );
      /**
       * Model end
       */

      /**
       * Entity start
       */
      (new GenerateEntityProcessor($repositoryDir))
        ->process(
          (new GenerateEntityMessage())
            ->setEntityName($repositoryName . 'Entity')
            ->setNamespace($repositoryNamespace)
            ->setProperties($entityColumns)
        );
      /**
       * Entity end
       */

      /**
       * Enum start
       */
      foreach ($repository->enums as $enum)
      {
        (new GenerateEnumProcessor($repositoryDir))
          ->process(
            (new GenerateEnumMessage())
              ->setNamespace($repositoryNamespace)
              ->setEnumName($enum->name)
              ->setConstants($enum->constants)
        );
      }
      /**
       * Enum end
       */

      /**
       * Collection start
       */
      (new GenerateCollectionProcessor($repositoryDir))
        ->process(
          (new GenerateCollectionMessage())
            ->setCollectionNamespace($repositoryNamespace)
            ->setEntityName($repositoryName . 'Entity')
        );
      /**
       * Collection end
       */

      /**
       * Repository start
       */
      (new GenerateRepositoryProcessor($repositoryDir))
        ->process(
          (new GenerateRepositoryMessage())
            ->setNamespace($repositoryNamespace)
            ->setRepoName($repositoryName)
            ->setColumns($repository->columns)
            ->setModuleNamespace(
              $namespace . '\\' . $moduleName . 'Module'
            )
            ->setModuleName($moduleName . 'Module')
            ->setModelName($repositoryName . 'Model')
            ->setModelNamespace($repositoryNamespace)
            ->setEntityName($repositoryName . 'Entity')
            ->setEntityNamespace($repositoryNamespace)
            ->setCollectionName($repositoryName . 'EntityCollection')
            ->setCollectionNamespace($repositoryNamespace)
        );
      /**
       * Repository end
       */

      /**
       * Migration start
       */
      (new GenerateMigrationProcessor(__DIR__ . '/../../../../../'))
        ->process(
          (new GenerateMigrationMessage())
            ->setDatabaseName($this->moduleConfig->database)
            ->setTableName($repository->table)
            ->setOldColumns($previousMigrationColumns)
            ->setNewColumns($newMigrationColumns)
        );
      /**
       * Migration end
       */
    }

    // Finally save the build file we just used to compare next time we build
    yaml_emit_file(
      str_replace('.yaml', '-previous.yaml', $message->getBuildFile()),
      json_decode(json_encode(['module' => $this->moduleConfig]), true)
    );

    echo 'Make sure you:
  - Add the module to (product)Module.php
  - Add the module class in (Abstract/product)Product.php
  - Add database configs
  - Check migrations, set nullables/indexes, run migrations
  - Test the tables
  - Reformat all generated files
  - Add everything to git
';

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateModuleMessage $message
   *
   * @throws RequiredValueMissingException
   * @throws InvalidMessageValueException
   * @returns GenerateModuleMessage
   */
  public function validate(GenerateModuleMessage $message)
  {
    if (empty($message->getBuildFile()))
    {
      throw new RequiredValueMissingException('buildFile');
    }

    if (!is_file($message->getBuildFile()))
    {
      throw new InvalidMessageValueException('buildFile must be a file');
    }

    $config = yaml_parse_file($message->getBuildFile());

    if (!is_array($config))
    {
      throw new InvalidMessageValueException(
        'buildFile must be a valid yaml config file'
      );
    }

    $this->moduleConfig = $config = json_decode(json_encode($config['module']));

    $previousBuildFile = str_replace('.yaml', '-previous.yaml', $message->getBuildFile());

    if (is_file($previousBuildFile))
    {
      $previousConfig = yaml_parse_file($previousBuildFile);

      if (!is_array($previousConfig))
      {
        throw new InvalidMessageValueException(
          'previous buildFile must be a valid yaml config file if the file exists (build-previous.yaml)'
        );
      }

      $this->previousConfig = json_decode(json_encode($previousConfig['module']));
    }

    if (!isset($config))
    {
      die(
        "Missing module: key, example config: " . $this->exampleConfig()
      );
    }

    if (!isset($config->name))
    {
      die(
        "Missing module:name: key, example config: " . $this->exampleConfig()
      );
    }

    if (!isset($config->namespace))
    {
      die(
        "Missing module:namespace: key, example config: "
        . $this->exampleConfig()
      );
    }

    if (!isset($config->database))
    {
      die(
        "Missing module:database: key, example config: "
        . $this->exampleConfig()
      );
    }

    if (!isset($config->repositories))
    {
      die(
        "Missing module:repositories: key, example config: "
        . $this->exampleConfig()
      );
    }

    if (count((array)$config->repositories))
    {
      if (!count($config->repositories))
      {
        die(
          "Missing module:repositories: array, example config: "
          . $this->exampleConfig()
        );
      }

      $usedNames = [];

      foreach ($config->repositories as $name => $repository)
      {
        if (in_array($name, $usedNames))
        {
          die("Duplicate repository name found: " . $name);
        }

        $usedNames[] = $name;

        if (!isset($repository->table))
        {
          die(
            "Missing module:name:repositories:{$name}:table key, example config: "
            . $this->exampleConfig()
          );
        }

        if (!isset($repository->columns))
        {
          die(
            "Missing module:name:repositories:{$name}:columns key, example config: "
            . $this->exampleConfig()
          );
        }

        if (!count($repository->columns))
        {
          die(
            "Missing module:name:repositories:{$name}:columns array, example config: "
            . $this->exampleConfig()
          );
        }

        $usedColumns = [];

        foreach ($repository->columns as $columnName => $column)
        {
          if (in_array($columnName, $usedColumns))
          {
            die("Duplicate column name found: " . $columnName);
          }

          $usedColumns[] = $columnName;

          if (!stristr($column, ' '))
          {
            die(
              "Columns should be defined like: (plural|unique) (int|string|datetime) (EntityType)? "
              . "Example config: " . $this->exampleConfig() . PHP_EOL .
              "Offending repository:column " . $name . ':' . $columnName . PHP_EOL
            );
          }
        }
      }
    }

    return $message;
  }

  /**
   * @return string
   */
  private function exampleConfig()
  {
    return "

module:
  name: Service
  namespace: Codingbeard\\Framework\\Module\\Service
  database: service
  repositories:
    userService:
      table: service
      columns:
        id: unique int
        userId: plural int
        serviceCode: plural string
        billingServiceId: unique int
        created: plural datetime
        status: plural string CodingbeardServiceStatusEnum
      enums:
        CodingbeardServiceStatusEnum:
          - active
          - suspended
          - cancelled
";
  }

}
