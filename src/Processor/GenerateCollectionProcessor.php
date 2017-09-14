<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Framework\Module\Processor\ProcessorInterface;
use Codingbeard\Framework\Module\Product\ProductAwareTrait;
use Codingbeard\Framework\Module\Product\ProductInterface;
use Codingbeard\Devtools\Message\GenerateCollectionMessage;

/**
 * @method ProductInterface getProduct()
 */
class GenerateCollectionProcessor extends AbstractDevtoolProcessor
{

  /**
   * @devtoolsOverwritable
   *
   * @return GenerateCollectionMessage
   */
  public function getMessageClass()
  {
    return new GenerateCollectionMessage;
  }

  /**
   * @param GenerateCollectionMessage $message
   *
   * @return bool
   */
  public function process(GenerateCollectionMessage $message)
  {
    $this->validate($message);

    $namespace = $message->getCollectionNamespace();
    $name = $message->getEntityName();

    $namespace = rtrim($namespace, '\\');

    $generator = new DevtoolTemplateGenerator($this->parentDir);

    $generator->generateFile(
      $name . 'Collection.php',
      'Collection',
      'collection',
      [
        'collection' => [
          'namespace' => $namespace,
        ],
        'entity'     => [
          'name' => $name,
        ],
      ]
    );

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateCollectionMessage $message
   *
   * @throws \Exception
   * @returns GenerateCollectionMessage
   */
  public function validate(GenerateCollectionMessage $message)
  {
    if (empty($message->getCollectionNamespace()))
    {
      throw new \Exception('collectionNamespace');
    }

    if (empty($message->getEntityName()))
    {
      throw new \Exception('entityName');
    }

    $message->setCollectionNamespace(rtrim($message->getCollectionNamespace(), '\\'));

    return $message;
  }

}
