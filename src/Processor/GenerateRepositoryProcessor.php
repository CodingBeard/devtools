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
   * @throws RequiredValueMissingException
   * @returns GenerateRepositoryMessage
   */
  public function validate(GenerateRepositoryMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new RequiredValueMissingException('namespace');
    }

    if (empty($message->getRepoName()))
    {
      throw new RequiredValueMissingException('repoName');
    }

    if (empty($message->getColumns()))
    {
      throw new RequiredValueMissingException('columns');
    }

    if (empty($message->getModuleNamespace()))
    {
      throw new RequiredValueMissingException('moduleNamespace');
    }

    if (empty($message->getModuleName()))
    {
      throw new RequiredValueMissingException('moduleName');
    }

    if (empty($message->getModelNamespace()))
    {
      throw new RequiredValueMissingException('modelNamespace');
    }

    if (empty($message->getModelName()))
    {
      throw new RequiredValueMissingException('modelName');
    }

    if (empty($message->getEntityNamespace()))
    {
      throw new RequiredValueMissingException('entityNamespace');
    }

    if (empty($message->getEntityName()))
    {
      throw new RequiredValueMissingException('entityName');
    }

    if (empty($message->getCollectionNamespace()))
    {
      throw new RequiredValueMissingException('collectionNamespace');
    }

    if (empty($message->getCollectionName()))
    {
      throw new RequiredValueMissingException('collectionName');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));

    return $message;
  }

}
