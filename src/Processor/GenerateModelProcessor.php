<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Devtools\Message\GenerateModelMessage;

class GenerateModelProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateModelMessage
   */
  public function getMessageClass()
  {
    return new GenerateModelMessage;
  }

  /**
   * @param GenerateModelMessage $message
   *
   * @return bool
   */
  public function process(GenerateModelMessage $message)
  {
    $this->validate($message);

    $moduleNamespace = $message->getModuleNamespace();
    $moduleName = $message->getModuleName();
    $repositoryName = $message->getRepositoryName();
    $tableName = $message->getTableName();
    $name = $message->getModelName();
    $cliProperties = $message->getProperties();

    if (substr(strtolower($name), -5) != 'Model')
    {
      $name .= 'Model';
    }

    $properties = [];

    foreach ($cliProperties as $cliProperty)
    {
      if (substr_count($cliProperty, ':') != 2)
      {
        exit("All params after model name should be in format name:type:[unique|plural]" . PHP_EOL);
      }

      $parts = explode(':', $cliProperty);

      $property = new \stdClass();
      $property->name = $parts[0];
      $property->type = $parts[1];
      $property->unique = ($parts[2] == 'unique' ? true : false);

      $properties[] = $property;
    }

    $doubles = [];

    foreach ($properties as $property)
    {
      foreach ($properties as $property2)
      {
        if (!in_array((object)['one' => $property, 'two' => $property2], $doubles))
        {
          if (!in_array((object)['one' => $property2, 'two' => $property], $doubles))
          {
            if ($property != $property2)
            {
              $doubles[] = (object)['one' => $property2, 'two' => $property];
            }
          }
        }
      }
    }

    $namespace = sprintf(
      '%s\\Data\\%s',
      $moduleNamespace,
      str_replace($moduleName, '', $repositoryName)
    );

    $generator = new DevtoolTemplateGenerator($this->parentDir);

    $generator->generateFile(
      $name . '.php',
      'Model',
      'model',
      [
        'module' => [
          'namespace'  => $moduleNamespace,
          'name'  => $moduleName,
        ],
        'repository' => [
          'name'  => $repositoryName,
          'table'      => $tableName,
        ],
        'model' => [
          'name'       => $name,
          'namespace'  => $namespace,
          'properties' => $properties,
          'doubles' => $doubles
        ],
      ]
    );
    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateModelMessage $message
   *
   * @throws \Exception
   * @returns GenerateModelMessage
   */
  public function validate(GenerateModelMessage $message)
  {
    if (empty($message->getModuleNamespace()))
    {
      throw new \Exception('moduleNamespace');
    }

    if (empty($message->getModuleName()))
    {
      throw new \Exception('moduleName');
    }

    if (empty($message->getRepositoryName()))
    {
      throw new \Exception('repositoryName');
    }

    if (empty($message->getTableName()))
    {
      throw new \Exception('tableName');
    }

    if (empty($message->getModelName()))
    {
      throw new \Exception('modelName');
    }

    if (empty($message->getProperties()))
    {
      throw new \Exception('properties');
    }

    $message->setModuleNamespace(rtrim($message->getModuleNamespace(), '\\'));

    return $message;
  }

}
