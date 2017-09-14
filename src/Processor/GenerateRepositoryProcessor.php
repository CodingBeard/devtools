<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Devtools\Message\GenerateRepositoryMessage;

class GenerateRepositoryProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   * @return GenerateRepositoryMessage
   */
  public function getMessageClass()
  {
    return new GenerateRepositoryMessage;
  }

  /**
   * @param GenerateRepositoryMessage $message
   *
   * @return bool
   */
  public function process(GenerateRepositoryMessage $message)
  {
    $this->validate($message);

    $repositoryName = $message->getRepoName() . 'Repository';

    $uses = [];

    foreach ($message->getColumns() as $column)
    {
      if ($column->entityType)
      {

        if ($column->entityType == 'Carbon')
        {
          $use = 'Carbon\\Carbon';

          if (!in_array($use, $uses))
          {
            $uses[] = $use;
          }
        }
        elseif (substr($column->entityType, -4) == 'Enum')
        {
          $use = $message->getNamespace() . $column->entityType;

          if (!in_array($use, $uses))
          {
            $uses[] = $use;
          }
        }
        else
        {
          $use = $message->getNamespace() . '\\' . $column->entityType;

          if (!in_array($use, $uses))
          {
            $uses[] = $use;
          }
        }
      }
    }

    $generator = new DevtoolTemplateGenerator($this->parentDir);

    $data = [
      'repository' => [
        'namespace' => $message->getNamespace(),
        'name'      => $repositoryName,
        'columns'   => $message->getColumns(),
        'uses'      => $uses,
      ],
      'module'     => [
        'name'      => $message->getModuleName(),
      ],
      'model'      => [
        'name'      => $message->getModelName(),
      ],
      'entity'     => [
        'name'      => $message->getEntityName(),
      ],
      'collection' => [
        'name'      => $message->getCollectionName(),
      ],
    ];
    $generator->generateFile(
      $repositoryName . '.php',
      'Repository',
      'repository',
      $data
    );

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateRepositoryMessage $message
   *
   * @throws \Exception
   * @returns GenerateRepositoryMessage
   */
  public function validate(GenerateRepositoryMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new \Exception('namespace');
    }

    if (empty($message->getRepoName()))
    {
      throw new \Exception('repoName');
    }

    if (empty($message->getColumns()))
    {
      throw new \Exception('columns');
    }

    if (empty($message->getModuleNamespace()))
    {
      throw new \Exception('moduleNamespace');
    }

    if (empty($message->getModuleName()))
    {
      throw new \Exception('moduleName');
    }

    if (empty($message->getModelNamespace()))
    {
      throw new \Exception('modelNamespace');
    }

    if (empty($message->getModelName()))
    {
      throw new \Exception('modelName');
    }

    if (empty($message->getEntityNamespace()))
    {
      throw new \Exception('entityNamespace');
    }

    if (empty($message->getEntityName()))
    {
      throw new \Exception('entityName');
    }

    if (empty($message->getCollectionNamespace()))
    {
      throw new \Exception('collectionNamespace');
    }

    if (empty($message->getCollectionName()))
    {
      throw new \Exception('collectionName');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));

    return $message;
  }

}
