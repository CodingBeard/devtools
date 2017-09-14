<?php

namespace Codingbeard\Devtools\Generator\Visitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class AdditionVisitor extends AbstractDevtoolsVisitor
{
  /** @var array */
  protected $existingConstants = [];

  /** @var array */
  protected $existingProperties = [];

  /** @var array */
  protected $existingFunctions = [];

  /** @var array */
  protected $existingUses = [];

  /** @var array */
  protected $newConstants = [];

  /** @var array */
  protected $newProperties = [];

  /** @var array */
  protected $newFunctions = [];

  /** @var array */
  protected $newUses = [];

  /** @var array */
  protected $additionalConstants = [];

  /** @var array */
  protected $additionalProperties = [];

  /** @var array */
  protected $additionalFunctions = [];

  /** @var array */
  protected $additionalUses = [];

  /**
   * AbstractDevtoolsVisitor constructor.
   *
   * @param Node[] $existingClass
   * @param Node[] $newClass
   */
  public function __construct($existingClass, $newClass)
  {
    parent::__construct($existingClass, $newClass);

    foreach ($existingClass[0]->stmts as $node)
    {
      if ($node instanceof Use_)
      {
        $this->existingUses[$this->getName($node)] = $node;
      }
    }

    foreach ($this->existingClass->stmts as $node)
    {
      if ($node instanceof ClassConst)
      {
        $this->existingConstants[$this->getName($node)] = $node;
      }
      elseif ($node instanceof Property)
      {
        $this->existingProperties[$this->getName($node)] = $node;
      }
      elseif ($node instanceof ClassMethod)
      {
        $this->existingFunctions[$this->getName($node)] = $node;
      }
    }

    foreach ($newClass[0]->stmts as $node)
    {
      if ($node instanceof Use_)
      {
        $this->newUses[$this->getName($node)] = $node;
      }
    }

    foreach ($this->newClass->stmts as $node)
    {
      if ($node instanceof ClassConst)
      {
        $this->newConstants[$this->getName($node)] = $node;
      }
      elseif ($node instanceof Property)
      {
        $this->newProperties[$this->getName($node)] = $node;
      }
      elseif ($node instanceof ClassMethod)
      {
        $this->newFunctions[$this->getName($node)] = $node;
      }
      elseif ($node instanceof Use_)
      {
        $this->newUses[$this->getName($node)] = $node;
      }
    }

    $after = null;

    foreach ($this->newConstants as $name => $node)
    {
      if (!isset($this->existingConstants[$name]))
      {
        $x = 0;
        $this->additionalConstants[] = (object)[
          'name'  => $name,
          'node'  => $node,
          'after' => $after,
        ];
      }

      $after = $name;
    }

    $after = null;

    foreach ($this->newProperties as $name => $node)
    {
      if (!isset($this->existingProperties[$name]))
      {
        $this->additionalProperties[] = (object)[
          'name'  => $name,
          'node'  => $node,
          'after' => $after,
        ];
      }

      $after = $name;
    }

    $after = null;

    foreach ($this->newFunctions as $name => $node)
    {
      if (!isset($this->existingFunctions[$name]))
      {
        $this->additionalFunctions[] = (object)[
          'name'  => $name,
          'node'  => $node,
          'after' => $after,
        ];
      }

      $after = $name;
    }

    $after = null;

    foreach ($this->newUses as $name => $node)
    {
      if (!isset($this->existingUses[$name]))
      {
        $x = 0;
        $this->additionalUses[] = (object)[
          'name'  => $name,
          'node'  => $node,
          'after' => $after,
        ];
      }

      $after = $name;
    }
  }

  /**
   * @param Node[] $nodes
   *
   * @return Node[]
   */
  public function afterTraverse(array $nodes)
  {
    $newNodes = [];

    if (count($this->additionalUses))
    {
      $x = 0;
      foreach ($this->additionalUses as $use)
      {
        $nodes = $this->addUse($use->node, $nodes);
      }

      $this->additionalUses = [];
    }

    if (count($this->additionalConstants))
    {
      foreach ($this->additionalConstants as $additionalConstant)
      {
        $nodes = $this->addConstant($additionalConstant->node, $nodes);
      }
    }
    if (count($this->additionalProperties))
    {
      foreach ($this->additionalProperties as $additionalProperty)
      {
        $nodes = $this->addProperty($additionalProperty->node, $nodes);
      }
    }
    if (count($this->additionalFunctions))
    {
      foreach ($this->additionalFunctions as $additionalFunction)
      {
        $nodes = $this->addFunction($additionalFunction->node, $nodes);
      }
    }

    return $nodes;
  }

  /**
   * @param Node $node
   *
   * @return bool|null|Node
   */
  public function leaveNode(Node $node)
  {
    if ($node instanceof ClassConst)
    {
      foreach ($this->additionalConstants as $key => $additional)
      {
        if ($additional->after == null)
        {
          unset($this->additionalConstants[$key]);

          return [
            $node,
            $additional->node,
          ];
        }
        elseif ($this->getName($node) == $additional->after)
        {
          unset($this->additionalConstants[$key]);

          return [
            $node,
            $additional->node,
          ];
        }
      }
    }
    elseif ($node instanceof Property)
    {
      foreach ($this->additionalProperties as $key => $additional)
      {
        if ($additional->after == null)
        {
          unset($this->additionalProperties[$key]);

          return [
            $node,
            $additional->node,
          ];
        }
        elseif ($this->getName($node) == $additional->after)
        {
          unset($this->additionalProperties[$key]);

          return [
            $node,
            $additional->node,
          ];
        }
      }
    }
    elseif ($node instanceof ClassMethod)
    {
      foreach ($this->additionalFunctions as $key => $additional)
      {
        if ($additional->after == null)
        {
          unset($this->additionalFunctions[$key]);

          return [
            $node,
            $additional->node,
          ];
        }
        elseif ($this->getName($node) == $additional->after)
        {
          unset($this->additionalFunctions[$key]);

          return [
            $node,
            $additional->node,
          ];
        }
      }
    }

    return null;
  }

}
