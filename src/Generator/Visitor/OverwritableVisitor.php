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

class OverwritableVisitor extends AbstractDevtoolsVisitor
{
  /**
   * @param Node $node
   *
   * @return bool|null|Node
   */
  public function leaveNode(Node $node)
  {
    if (
      $node instanceof Property
      ||
      $node instanceof ClassMethod
      ||
      $node instanceof ClassConst
    )
    {
      $doc = $node->getDocComment();

      if ($doc instanceof Doc)
      {
        if (!stristr($doc->getText(), '@devtoolsOverwritable'))
        {
          return null;
        }
      }
      else
      {
        return null;
      }

      foreach ($this->newClass->stmts as $newNode)
      {
        if (
          $newNode instanceof Property
          ||
          $newNode instanceof ClassMethod
          ||
          $newNode instanceof ClassConst
        )
        {
          if (get_class($node) == get_class($newNode))
          {
            if ($this->getName($node) == $this->getName($newNode))
            {
              return $newNode;
            }
          }
        }
      }

      return NodeTraverser::REMOVE_NODE;
    }

    return null;
  }

}
