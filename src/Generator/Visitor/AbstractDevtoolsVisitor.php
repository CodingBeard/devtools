<?php

namespace Codingbeard\Devtools\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;

class AbstractDevtoolsVisitor extends NodeVisitorAbstract
{
  /** @var Node\Stmt\Class_ */
  protected $existingClass;

  /** @var Node\Stmt\Class_ */
  protected $newClass;

  /** @var string[] */
  protected $uses;

  /**
   * AbstractDevtoolsVisitor constructor.
   *
   * @param Node[] $existingClass
   * @param Node[] $newClass
   */
  public function __construct($existingClass, $newClass)
  {
    $this->existingClass = $this->findClass($existingClass);
    $this->newClass = $this->findClass($newClass);

    foreach ($this->existingClass->stmts as $stmt)
    {
      if ($stmt instanceof Use_)
      {
        $this->uses[] = $this->getName($stmt);
      }
    }
  }

  /**
   * @param Node|Node[] $nodes
   *
   * @return Node
   * @throws \Exception
   */
  private function findClass($nodes)
  {
    if ($nodes instanceof Class_)
    {
      return $nodes;
    }
    else
    {
      if (is_array($nodes))
      {
        foreach ($nodes as $node)
        {
          $class = $this->findClass($node);

          if ($class instanceof Class_)
          {
            return $class;
          }
        }
      }
      elseif ($nodes instanceof Node)
      {
        if (isset($nodes->stmts))
        {
          foreach ($nodes->stmts as $stmt)
          {
            $class = $this->findClass($stmt);

            if ($class instanceof Class_)
            {
              return $class;
            }
          }
        }
        else
        {
          return false;
        }
      }
    }

    throw new \Exception('Could not find the class in the nodes');
  }

  /**
   * @param Node $node
   *
   * @return string
   * @throws \Exception
   */
  protected function getName(Node $node)
  {
    if ($node instanceof ClassConst)
    {
      return $node->consts[0]->name;
    }
    elseif ($node instanceof Property)
    {
      return $node->props[0]->name;
    }
    elseif ($node instanceof ClassMethod)
    {
      return $node->name;
    }
    elseif ($node instanceof Use_)
    {
      return implode('\\', $node->uses[0]->name->parts);
    }

    throw new \Exception(
      'I do not know how to get the name from that type of node: '
      . get_class($node)
    );
  }

  /**
   * @param Use_   $use
   * @param Node[] $nodes
   *
   * @return Node[]
   * @throws \Exception
   */
  protected function addUse($use, $nodes)
  {
    if (is_array($nodes))
    {
      if ($nodes[0] instanceof Namespace_)
      {
        if (is_array($nodes[0]->stmts))
        {
          foreach ($nodes[0]->stmts as $key => $stmt)
          {
            if (isset($nodes[0]->stmts[$key + 1]))
            {
              if (!$nodes[0]->stmts[$key + 1] instanceof Use_)
              {
                array_splice($nodes[0]->stmts, $key, null, [$use]);

                return $nodes;
              }
            }
          }
        }
        else
        {
          throw new \Exception('You want a use statement in an empty file?');
        }
      }
      else
      {
        throw new \Exception('Why is there no namespace in your file?');
      }
    }
    else
    {
      throw new \Exception('I can not add a use stmt to a non-array!');
    }

    return $nodes;
  }

  /**
   * @param ClassConst $constant
   * @param Node[]     $nodes
   *
   * @return Node[]
   * @throws \Exception
   */
  protected function addConstant($constant, $nodes)
  {
    if (is_array($nodes))
    {
      if ($nodes[0] instanceof Namespace_)
      {
        if (is_array($nodes[0]->stmts))
        {
          foreach ($nodes[0]->stmts as $key => $classstmt)
          {
            if ($classstmt instanceof Class_)
            {
              foreach ($classstmt->stmts as $classkey => $classsubstmt)
              {
                if (isset($classstmt->stmts[$classkey + 1]))
                {
                  if (!$classstmt->stmts[$classkey + 1] instanceof ClassConst)
                  {
                    array_splice(
                      $nodes[0]->stmts[$key]->stmts,
                      $classkey,
                      null,
                      [$constant]
                    );

                    return $nodes;
                  }
                }
              }
            }
          }
        }
        else
        {
          throw new \Exception('You want a constant in an empty file?');
        }
      }
      else
      {
        throw new \Exception('Why is there no namespace in your file?');
      }
    }
    else
    {
      throw new \Exception('I can not add a use stmt to a non-array!');
    }

    return $nodes;
  }

  /**
   * @param Property $property
   * @param Node[]   $nodes
   *
   * @return Node[]
   * @throws \Exception
   */
  protected function addProperty($property, $nodes)
  {
    if (is_array($nodes))
    {
      if ($nodes[0] instanceof Namespace_)
      {
        if (is_array($nodes[0]->stmts))
        {
          foreach ($nodes[0]->stmts as $key => $classstmt)
          {
            if ($classstmt instanceof Class_)
            {
              foreach ($classstmt->stmts as $classkey => $classsubstmt)
              {
                if (isset($classstmt->stmts[$classkey + 1]))
                {
                  if (!$classstmt->stmts[$classkey + 1] instanceof Property)
                  {
                    array_splice(
                      $nodes[0]->stmts[$key]->stmts,
                      $classkey,
                      null,
                      [$property]
                    );

                    return $nodes;
                  }
                }
              }
            }
          }
        }
        else
        {
          throw new \Exception('You want a constant in an empty file?');
        }
      }
      else
      {
        throw new \Exception('Why is there no namespace in your file?');
      }
    }
    else
    {
      throw new \Exception('I can not add a use stmt to a non-array!');
    }

    return $nodes;
  }

  /**
   * @param ClassMethod $function
   * @param Node[]   $nodes
   *
   * @return Node[]
   * @throws \Exception
   */
  protected function addFunction($function, $nodes)
  {
    if (is_array($nodes))
    {
      if ($nodes[0] instanceof Namespace_)
      {
        if (is_array($nodes[0]->stmts))
        {
          foreach ($nodes[0]->stmts as $key => $classstmt)
          {
            if ($classstmt instanceof Class_)
            {
              foreach ($classstmt->stmts as $classkey => $classsubstmt)
              {
                if (isset($classstmt->stmts[$classkey + 1]))
                {
                  if (!$classstmt->stmts[$classkey + 1] instanceof ClassMethod)
                  {
                    array_splice(
                      $nodes[0]->stmts[$key]->stmts,
                      $classkey,
                      null,
                      [$function]
                    );

                    return $nodes;
                  }
                }
              }
            }
          }
        }
        else
        {
          throw new \Exception('You want a constant in an empty file?');
        }
      }
      else
      {
        throw new \Exception('Why is there no namespace in your file?');
      }
    }
    else
    {
      throw new \Exception('I can not add a use stmt to a non-array!');
    }

    return $nodes;
  }
}
