<?php

namespace Codingbeard\Devtools\Generator;

use ParaTest\Parser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Codingbeard\Devtools\Generator\Printer\CodingbeardPrinter;
use Codingbeard\Devtools\Generator\Template\DevtoolTemplateEngine;
use Codingbeard\Devtools\Generator\Visitor\AdditionVisitor;
use Codingbeard\Devtools\Generator\Visitor\OverwritableVisitor;

class DevtoolTemplateGenerator
{
  /** @var DevtoolTemplateEngine */
  protected $engine;

  /** @var Parser */
  protected $parser;

  /** @var NodeTraverser */
  protected $traverser;

  /** @var CodingbeardPrinter */
  protected $printer;

  /** @var string */
  protected $src;

  /**
   * DevtoolTemplateGenerator constructor.
   *
   * @param string $src
   */
  public function __construct($src)
  {
    $this->src = rtrim($src, '/') . '/';
    $this->engine = new DevtoolTemplateEngine();
    $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP5);
    $this->traverser = new NodeTraverser();

    $this->printer = new CodingbeardPrinter(
      [
        'shortArraySyntax' => true,
        'indent'           => '  ',
      ]
    );
  }

  /**
   * @param string $filePath
   * @param string $type
   * @param string $file
   * @param array|\stdClass $variables
   */
  public function generateFile($filePath, $type, $file, $variables)
  {
    $result = $this->engine->render($type, $file, $variables);

    $finalPath = $this->src . trim($filePath, '/');

    $this->buildFolders($finalPath);

    if (is_file($finalPath))
    {
      $result = $this->mergeClass(file_get_contents($finalPath), $result);
    }

    file_put_contents($finalPath, $result);
  }

  /**
   * @param $filePath
   */
  private function buildFolders($filePath)
  {
    $dir = dirname($filePath);

    while (!is_dir(dirname($filePath)))
    {
      if (is_dir(dirname($dir)))
      {
        mkdir($dir, 0777);
      }
      else
      {
        $dir = dirname($dir);
      }

      if (is_dir(dirname(dirname($filePath))))
      {
        if (!is_dir(dirname($filePath)))
        {
          mkdir(dirname($filePath), 0777);
        }
      }
    }
  }

  /**
   * @param string $existingClass
   * @param string $newClass
   *
   * @return string
   */
  private function mergeClass($existingClass, $newClass)
  {
    $existingParsed = $this->parser->parse($existingClass);
    $newParsed = $this->parser->parse($newClass);

    $this->traverser->addVisitor(
      new OverwritableVisitor($existingParsed, $newParsed)
    );

    $this->traverser->addVisitor(
      new AdditionVisitor($existingParsed, $newParsed)
    );

    $traversed = $this->traverser->traverse($existingParsed);

    $result = $this->printer->prettyPrintFile($traversed);

    return $result;
  }
}
