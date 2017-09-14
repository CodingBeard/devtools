<?php
namespace Codingbeard\Devtools\Processor;

use Codingbeard\Devtools\Generator\DevtoolTemplateGenerator;
use Codingbeard\Framework\Module\Processor\Exception\RequiredValueMissingException;
use Codingbeard\Framework\Module\Processor\ProcessorInterface;
use Codingbeard\Framework\Module\Product\ProductAwareTrait;
use Codingbeard\Framework\Module\Product\ProductInterface;
use Codingbeard\Devtools\Message\GenerateEntityMessage;

/**
 * @method ProductInterface getProduct()
 */
class GenerateEntityProcessor extends AbstractDevtoolProcessor
  implements ProcessorInterface
{
  use ProductAwareTrait;

  /**
   * @devtoolsOverwritable
   * @return GenerateEntityMessage
   */
  public function getMessageClass()
  {
    return new GenerateEntityMessage;
  }

  /**
   * @param GenerateEntityMessage $message
   *
   * @return bool
   */
  public function process(GenerateEntityMessage $message)
  {
    $this->validate($message);

    $namespace = $message->getNamespace();
    $name = $message->getEntityName();
    $cliProperties = $message->getProperties();

    $uses = [];
    $properties = [];

    foreach ($cliProperties as $cliProperty)
    {
      if (stripos($cliProperty, ':') === false)
      {
        exit("All params after entity name should be in format name:type" . PHP_EOL);
      }

      $property = new \stdClass();
      $property->name = explode(':', $cliProperty)[0];
      $property->type = explode(':', $cliProperty)[1];

      $properties[] = $property;

      if ($property->type == 'Carbon')
      {
        $use = 'Carbon\\Carbon';

        if (!in_array($use, $uses))
        {
          $uses[] = $use;
        }
      }
      elseif (substr($property->type, -4) == 'Enum')
      {
        $use = $namespace . $property->type;

        if (!in_array($use, $uses))
        {
          $uses[] = $use;
        }
      }
    }

    $generator = new DevtoolTemplateGenerator($this->parentDir);

    $generator->generateFile(
      $name . '.php',
      'Entity',
      'entity',
      [
        'entity' => [
          'namespace'  => $namespace,
          'uses'       => $uses,
          'name'       => $name,
          'properties' => $properties,
        ],
      ]
    );

    return true;
  }

  /**
   * @devtoolsOverwritable
   *
   * @param GenerateEntityMessage $message
   *
   * @throws RequiredValueMissingException
   * @returns GenerateEntityMessage
   */
  public function validate(GenerateEntityMessage $message)
  {
    if (empty($message->getNamespace()))
    {
      throw new RequiredValueMissingException('namespace');
    }

    if (empty($message->getEntityName()))
    {
      throw new RequiredValueMissingException('entityName');
    }

    if (empty($message->getProperties()))
    {
      throw new RequiredValueMissingException('properties');
    }

    $message->setNamespace(rtrim($message->getNamespace(), '\\'));

    return $message;
  }

}
