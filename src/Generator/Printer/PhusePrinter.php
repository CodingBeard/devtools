<?php

namespace Codingbeard\Devtools\Generator\Printer;

use PhpParser\PrettyPrinterAbstract;
use PhpParser\Node;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name;

class CodingbeardPrinter extends PrettyPrinterAbstract
{
  /**
   * Pretty prints an array of nodes (statements) and indents them optionally.
   *
   * @param Node[] $nodes  Array of nodes
   * @param bool   $indent Whether to indent the printed nodes
   *
   * @return string Pretty printed statements
   */
  protected function pStmts(array $nodes, $indent = true)
  {
    $output = '';
    foreach ($nodes as $node)
    {
      if ($node instanceof Stmt\Class_)
      {
        $output .= PHP_EOL;
      }

      $statement = $this->p($node) . ($node instanceof Expr ? ';' : '');

      if (
        $node instanceof Expr\MethodCall
        ||
        $node instanceof Stmt\Return_
      )
      {
        if (strlen($statement) > 80)
        {
          $statement = preg_replace(
            '#->#',
            PHP_EOL . $this->options['indent'] . '->',
            $statement,
            1
          );

          $statement = preg_replace(
            '#\)->#',
            ')' . PHP_EOL . $this->options['indent'] . '->',
            $statement
          );
        }
      }

      $output .= PHP_EOL
        . $this->pComments($node->getAttribute('comments', []))
        . $statement;

      if (
        $node instanceof Stmt\ClassMethod
        || $node instanceof Stmt\Property
        || $node instanceof Stmt\ClassConst
      )
      {
        $output .= PHP_EOL;
      }
    }

    if ($indent)
    {
      return preg_replace(
        '~\n(?!$|' . $this->noIndentToken . ')~',
        "\n" . $this->options['indent'],
        $output
      );
    }
    else
    {
      return $output;
    }
  }

  // Special nodes

  /**
   * @param Node\Param $node
   *
   * @return string
   */
  public function pParam(Node\Param $node)
  {
    return ($node->type ? $this->pType($node->type) . ' ' : '')
    . ($node->byRef ? '&' : '')
    . ($node->variadic ? '...' : '')
    . '$' . $node->name
    . ($node->default ? ' = ' . $this->p($node->default) : '');
  }

  /**
   * @param Node\Arg $node
   *
   * @return string
   */
  public function pArg(Node\Arg $node)
  {
    return ($node->byRef ? '&' : '') . ($node->unpack ? '...' : '') . $this->p(
      $node->value
    );
  }

  /**
   * @param Node\Const_ $node
   *
   * @return string
   */
  public function pConst(Node\Const_ $node)
  {
    return $node->name . ' = ' . $this->p($node->value);
  }

  // Names

  /**
   * @param Name $node
   *
   * @return string
   */
  public function pName(Name $node)
  {
    return implode('\\', $node->parts);
  }

  /**
   * @param Name\FullyQualified $node
   *
   * @return string
   */
  public function pName_FullyQualified(Name\FullyQualified $node)
  {
    return '\\' . implode('\\', $node->parts);
  }

  /**
   * @param Name\Relative $node
   *
   * @return string
   */
  public function pName_Relative(Name\Relative $node)
  {
    return 'namespace\\' . implode('\\', $node->parts);
  }

  // Magic Constants

  /**
   * @param MagicConst\Class_ $node
   *
   * @return string
   */
  public function pScalar_MagicConst_Class(MagicConst\Class_ $node)
  {
    return '__CLASS__';
  }

  /**
   * @param MagicConst\Dir $node
   *
   * @return string
   */
  public function pScalar_MagicConst_Dir(MagicConst\Dir $node)
  {
    return '__DIR__';
  }

  /**
   * @param MagicConst\File $node
   *
   * @return string
   */
  public function pScalar_MagicConst_File(MagicConst\File $node)
  {
    return '__FILE__';
  }

  /**
   * @param MagicConst\Function_ $node
   *
   * @return string
   */
  public function pScalar_MagicConst_Function(MagicConst\Function_ $node)
  {
    return '__FUNCTION__';
  }

  /**
   * @param MagicConst\Line $node
   *
   * @return string
   */
  public function pScalar_MagicConst_Line(MagicConst\Line $node)
  {
    return '__LINE__';
  }

  /**
   * @param MagicConst\Method $node
   *
   * @return string
   */
  public function pScalar_MagicConst_Method(MagicConst\Method $node)
  {
    return '__METHOD__';
  }

  /**
   * @param MagicConst\Namespace_ $node
   *
   * @return string
   */
  public function pScalar_MagicConst_Namespace(MagicConst\Namespace_ $node)
  {
    return '__NAMESPACE__';
  }

  /**
   * @param MagicConst\Trait_ $node
   *
   * @return string
   */
  public function pScalar_MagicConst_Trait(MagicConst\Trait_ $node)
  {
    return '__TRAIT__';
  }

  // Scalars

  /**
   * @param Scalar\String_ $node
   *
   * @return string
   */
  public function pScalar_String(Scalar\String_ $node)
  {
    return '\'' . $this->pNoIndent(addcslashes($node->value, '\'\\')) . '\'';
  }

  /**
   * @param Scalar\Encapsed $node
   *
   * @return string
   */
  public function pScalar_Encapsed(Scalar\Encapsed $node)
  {
    return '"' . $this->pEncapsList($node->parts, '"') . '"';
  }

  /**
   * @param Scalar\LNumber $node
   *
   * @return string
   */
  public function pScalar_LNumber(Scalar\LNumber $node)
  {
    return (string)$node->value;
  }

  /**
   * @param Scalar\DNumber $node
   *
   * @return string
   */
  public function pScalar_DNumber(Scalar\DNumber $node)
  {
    $stringValue = sprintf('%.16G', $node->value);
    if ($node->value !== (double)$stringValue)
    {
      $stringValue = sprintf('%.17G', $node->value);
    }

    // ensure that number is really printed as float
    return preg_match(
      '/^-?[0-9]+$/',
      $stringValue
    ) ? $stringValue . '.0' : $stringValue;
  }

  // Assignments

  /**
   * @param Expr\Assign $node
   *
   * @return string
   */
  public function pExpr_Assign(Expr\Assign $node)
  {
    return $this->pInfixOp('Expr_Assign', $node->var, ' = ', $node->expr);
  }

  /**
   * @param Expr\AssignRef $node
   *
   * @return string
   */
  public function pExpr_AssignRef(Expr\AssignRef $node)
  {
    return $this->pInfixOp('Expr_AssignRef', $node->var, ' =& ', $node->expr);
  }

  /**
   * @param AssignOp\Plus $node
   *
   * @return string
   */
  public function pExpr_AssignOp_Plus(AssignOp\Plus $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_Plus',
      $node->var,
      ' += ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\Minus $node
   *
   * @return string
   */
  public function pExpr_AssignOp_Minus(AssignOp\Minus $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_Minus',
      $node->var,
      ' -= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\Mul $node
   *
   * @return string
   */
  public function pExpr_AssignOp_Mul(AssignOp\Mul $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_Mul',
      $node->var,
      ' *= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\Div $node
   *
   * @return string
   */
  public function pExpr_AssignOp_Div(AssignOp\Div $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_Div',
      $node->var,
      ' /= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\Concat $node
   *
   * @return string
   */
  public function pExpr_AssignOp_Concat(AssignOp\Concat $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_Concat',
      $node->var,
      ' .= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\Mod $node
   *
   * @return string
   */
  public function pExpr_AssignOp_Mod(AssignOp\Mod $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_Mod',
      $node->var,
      ' %= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\BitwiseAnd $node
   *
   * @return string
   */
  public function pExpr_AssignOp_BitwiseAnd(AssignOp\BitwiseAnd $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_BitwiseAnd',
      $node->var,
      ' &= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\BitwiseOr $node
   *
   * @return string
   */
  public function pExpr_AssignOp_BitwiseOr(AssignOp\BitwiseOr $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_BitwiseOr',
      $node->var,
      ' |= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\BitwiseXor $node
   *
   * @return string
   */
  public function pExpr_AssignOp_BitwiseXor(AssignOp\BitwiseXor $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_BitwiseXor',
      $node->var,
      ' ^= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\ShiftLeft $node
   *
   * @return string
   */
  public function pExpr_AssignOp_ShiftLeft(AssignOp\ShiftLeft $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_ShiftLeft',
      $node->var,
      ' <<= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\ShiftRight $node
   *
   * @return string
   */
  public function pExpr_AssignOp_ShiftRight(AssignOp\ShiftRight $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_ShiftRight',
      $node->var,
      ' >>= ',
      $node->expr
    );
  }

  /**
   * @param AssignOp\Pow $node
   *
   * @return string
   */
  public function pExpr_AssignOp_Pow(AssignOp\Pow $node)
  {
    return $this->pInfixOp(
      'Expr_AssignOp_Pow',
      $node->var,
      ' **= ',
      $node->expr
    );
  }

  // Binary expressions

  /**
   * @param BinaryOp\Plus $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Plus(BinaryOp\Plus $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Plus',
      $node->left,
      ' + ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Minus $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Minus(BinaryOp\Minus $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Minus',
      $node->left,
      ' - ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Mul $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Mul(BinaryOp\Mul $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Mul',
      $node->left,
      ' * ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Div $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Div(BinaryOp\Div $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Div',
      $node->left,
      ' / ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Concat $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Concat(BinaryOp\Concat $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Concat',
      $node->left,
      ' . ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Mod $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Mod(BinaryOp\Mod $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Mod',
      $node->left,
      ' % ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\BooleanAnd $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_BooleanAnd(BinaryOp\BooleanAnd $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_BooleanAnd',
      $node->left,
      ' && ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\BooleanOr $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_BooleanOr(BinaryOp\BooleanOr $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_BooleanOr',
      $node->left,
      ' || ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\BitwiseAnd $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_BitwiseAnd(BinaryOp\BitwiseAnd $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_BitwiseAnd',
      $node->left,
      ' & ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\BitwiseOr $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_BitwiseOr(BinaryOp\BitwiseOr $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_BitwiseOr',
      $node->left,
      ' | ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\BitwiseXor $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_BitwiseXor(BinaryOp\BitwiseXor $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_BitwiseXor',
      $node->left,
      ' ^ ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\ShiftLeft $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_ShiftLeft(BinaryOp\ShiftLeft $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_ShiftLeft',
      $node->left,
      ' << ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\ShiftRight $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_ShiftRight(BinaryOp\ShiftRight $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_ShiftRight',
      $node->left,
      ' >> ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Pow $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Pow(BinaryOp\Pow $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Pow',
      $node->left,
      ' ** ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\LogicalAnd $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_LogicalAnd(BinaryOp\LogicalAnd $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_LogicalAnd',
      $node->left,
      ' and ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\LogicalOr $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_LogicalOr(BinaryOp\LogicalOr $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_LogicalOr',
      $node->left,
      ' or ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\LogicalXor $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_LogicalXor(BinaryOp\LogicalXor $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_LogicalXor',
      $node->left,
      ' xor ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Equal $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Equal(BinaryOp\Equal $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Equal',
      $node->left,
      ' == ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\NotEqual $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_NotEqual(BinaryOp\NotEqual $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_NotEqual',
      $node->left,
      ' != ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Identical $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Identical(BinaryOp\Identical $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Identical',
      $node->left,
      ' === ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\NotIdentical $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_NotIdentical(BinaryOp\NotIdentical $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_NotIdentical',
      $node->left,
      ' !== ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Spaceship $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Spaceship(BinaryOp\Spaceship $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Spaceship',
      $node->left,
      ' <=> ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Greater $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Greater(BinaryOp\Greater $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Greater',
      $node->left,
      ' > ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\GreaterOrEqual $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_GreaterOrEqual(BinaryOp\GreaterOrEqual $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_GreaterOrEqual',
      $node->left,
      ' >= ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Smaller $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Smaller(BinaryOp\Smaller $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Smaller',
      $node->left,
      ' < ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\SmallerOrEqual $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_SmallerOrEqual(BinaryOp\SmallerOrEqual $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_SmallerOrEqual',
      $node->left,
      ' <= ',
      $node->right
    );
  }

  /**
   * @param BinaryOp\Coalesce $node
   *
   * @return string
   */
  public function pExpr_BinaryOp_Coalesce(BinaryOp\Coalesce $node)
  {
    return $this->pInfixOp(
      'Expr_BinaryOp_Coalesce',
      $node->left,
      ' ?? ',
      $node->right
    );
  }

  /**
   * @param Expr\Instanceof_ $node
   *
   * @return string
   */
  public function pExpr_Instanceof(Expr\Instanceof_ $node)
  {
    return $this->pInfixOp(
      'Expr_Instanceof',
      $node->expr,
      ' instanceof ',
      $node->class
    );
  }

  // Unary expressions

  /**
   * @param Expr\BooleanNot $node
   *
   * @return string
   */
  public function pExpr_BooleanNot(Expr\BooleanNot $node)
  {
    return $this->pPrefixOp('Expr_BooleanNot', '!', $node->expr);
  }

  /**
   * @param Expr\BitwiseNot $node
   *
   * @return string
   */
  public function pExpr_BitwiseNot(Expr\BitwiseNot $node)
  {
    return $this->pPrefixOp('Expr_BitwiseNot', '~', $node->expr);
  }

  /**
   * @param Expr\UnaryMinus $node
   *
   * @return string
   */
  public function pExpr_UnaryMinus(Expr\UnaryMinus $node)
  {
    return $this->pPrefixOp('Expr_UnaryMinus', '-', $node->expr);
  }

  /**
   * @param Expr\UnaryPlus $node
   *
   * @return string
   */
  public function pExpr_UnaryPlus(Expr\UnaryPlus $node)
  {
    return $this->pPrefixOp('Expr_UnaryPlus', '+', $node->expr);
  }

  /**
   * @param Expr\PreInc $node
   *
   * @return string
   */
  public function pExpr_PreInc(Expr\PreInc $node)
  {
    return $this->pPrefixOp('Expr_PreInc', '++', $node->var);
  }

  /**
   * @param Expr\PreDec $node
   *
   * @return string
   */
  public function pExpr_PreDec(Expr\PreDec $node)
  {
    return $this->pPrefixOp('Expr_PreDec', '--', $node->var);
  }

  /**
   * @param Expr\PostInc $node
   *
   * @return string
   */
  public function pExpr_PostInc(Expr\PostInc $node)
  {
    return $this->pPostfixOp('Expr_PostInc', $node->var, '++');
  }

  /**
   * @param Expr\PostDec $node
   *
   * @return string
   */
  public function pExpr_PostDec(Expr\PostDec $node)
  {
    return $this->pPostfixOp('Expr_PostDec', $node->var, '--');
  }

  /**
   * @param Expr\ErrorSuppress $node
   *
   * @return string
   */
  public function pExpr_ErrorSuppress(Expr\ErrorSuppress $node)
  {
    return $this->pPrefixOp('Expr_ErrorSuppress', '@', $node->expr);
  }

  /**
   * @param Expr\YieldFrom $node
   *
   * @return string
   */
  public function pExpr_YieldFrom(Expr\YieldFrom $node)
  {
    return $this->pPrefixOp('Expr_YieldFrom', 'yield from ', $node->expr);
  }

  /**
   * @param Expr\Print_ $node
   *
   * @return string
   */
  public function pExpr_Print(Expr\Print_ $node)
  {
    return $this->pPrefixOp('Expr_Print', 'print ', $node->expr);
  }

  // Casts

  /**
   * @param Cast\Int_ $node
   *
   * @return string
   */
  public function pExpr_Cast_Int(Cast\Int_ $node)
  {
    return $this->pPrefixOp('Expr_Cast_Int', '(int) ', $node->expr);
  }

  /**
   * @param Cast\Double $node
   *
   * @return string
   */
  public function pExpr_Cast_Double(Cast\Double $node)
  {
    return $this->pPrefixOp('Expr_Cast_Double', '(double) ', $node->expr);
  }

  /**
   * @param Cast\String_ $node
   *
   * @return string
   */
  public function pExpr_Cast_String(Cast\String_ $node)
  {
    return $this->pPrefixOp('Expr_Cast_String', '(string) ', $node->expr);
  }

  /**
   * @param Cast\Array_ $node
   *
   * @return string
   */
  public function pExpr_Cast_Array(Cast\Array_ $node)
  {
    return $this->pPrefixOp('Expr_Cast_Array', '(array) ', $node->expr);
  }

  /**
   * @param Cast\Object_ $node
   *
   * @return string
   */
  public function pExpr_Cast_Object(Cast\Object_ $node)
  {
    return $this->pPrefixOp('Expr_Cast_Object', '(object) ', $node->expr);
  }

  /**
   * @param Cast\Bool_ $node
   *
   * @return string
   */
  public function pExpr_Cast_Bool(Cast\Bool_ $node)
  {
    return $this->pPrefixOp('Expr_Cast_Bool', '(bool) ', $node->expr);
  }

  /**
   * @param Cast\Unset_ $node
   *
   * @return string
   */
  public function pExpr_Cast_Unset(Cast\Unset_ $node)
  {
    return $this->pPrefixOp('Expr_Cast_Unset', '(unset) ', $node->expr);
  }

  // Function calls and similar constructs

  /**
   * @param Expr\FuncCall $node
   *
   * @return string
   */
  public function pExpr_FuncCall(Expr\FuncCall $node)
  {
    return $this->pCallLhs($node->name)
    . '(' . $this->pCommaSeparated($node->args) . ')';
  }

  /**
   * @param Expr\MethodCall $node
   *
   * @return string
   */
  public function pExpr_MethodCall(Expr\MethodCall $node)
  {
    $args = $this->pCommaSeparated($node->args);

    if (strlen($args) > 80)
    {
      $args = PHP_EOL . $this->options['indent'] . $args . PHP_EOL;
    }

    return $this->pDereferenceLhs($node->var)
    . '->' . $this->pObjectProperty($node->name)
    . '(' . $args . ')';
  }

  /**
   * @param Expr\StaticCall $node
   *
   * @return string
   */
  public function pExpr_StaticCall(Expr\StaticCall $node)
  {
    return $this->pDereferenceLhs($node->class) . '::'
    . ($node->name instanceof Expr
      ? ($node->name instanceof Expr\Variable
        ? $this->p($node->name)
        : '{' . $this->p($node->name) . '}')
      : $node->name)
    . '(' . $this->pCommaSeparated($node->args) . ')';
  }

  /**
   * @param Expr\Empty_ $node
   *
   * @return string
   */
  public function pExpr_Empty(Expr\Empty_ $node)
  {
    return 'empty(' . $this->p($node->expr) . ')';
  }

  /**
   * @param Expr\Isset_ $node
   *
   * @return string
   */
  public function pExpr_Isset(Expr\Isset_ $node)
  {
    return 'isset(' . $this->pCommaSeparated($node->vars) . ')';
  }

  /**
   * @param Expr\Eval_ $node
   *
   * @return string
   */
  public function pExpr_Eval(Expr\Eval_ $node)
  {
    return 'eval(' . $this->p($node->expr) . ')';
  }

  /**
   * @param Expr\Include_ $node
   *
   * @return string
   */
  public function pExpr_Include(Expr\Include_ $node)
  {
    static $map = [
      Expr\Include_::TYPE_INCLUDE      => 'include',
      Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once',
      Expr\Include_::TYPE_REQUIRE      => 'require',
      Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once',
    ];

    return $map[$node->type] . ' ' . $this->p($node->expr);
  }

  /**
   * @param Expr\List_ $node
   *
   * @return string
   */
  public function pExpr_List(Expr\List_ $node)
  {
    $pList = [];
    foreach ($node->vars as $var)
    {
      if (null === $var)
      {
        $pList[] = '';
      }
      else
      {
        $pList[] = $this->p($var);
      }
    }

    return 'list(' . implode(', ', $pList) . ')';
  }

  // Other

  /**
   * @param Expr\Variable $node
   *
   * @return string
   */
  public function pExpr_Variable(Expr\Variable $node)
  {
    if ($node->name instanceof Expr)
    {
      return '${' . $this->p($node->name) . '}';
    }
    else
    {
      return '$' . $node->name;
    }
  }

  /**
   * @param Expr\Array_ $node
   *
   * @return string
   */
  public function pExpr_Array(Expr\Array_ $node)
  {
    $length = strlen($this->pCommaSeparated($node->items));

    if ($this->options['shortArraySyntax'])
    {
      if ($length > 120)
      {
        return '[' . PHP_EOL . '  ' .
        str_replace(
          ', ',
          ',' . PHP_EOL . '  ',
          $this->pCommaSeparated($node->items)
        )
        . PHP_EOL . ']';
      }
      else
      {
        return '[' . $this->pCommaSeparated($node->items) . ']';
      }
    }
    else
    {
      return 'array(' . $this->pCommaSeparated($node->items) . ')';
    }
  }

  /**
   * @param Expr\ArrayItem $node
   *
   * @return string
   */
  public function pExpr_ArrayItem(Expr\ArrayItem $node)
  {
    return (null !== $node->key ? $this->p($node->key) . ' => ' : '')
    . ($node->byRef ? '&' : '') . $this->p($node->value);
  }

  /**
   * @param Expr\ArrayDimFetch $node
   *
   * @return string
   */
  public function pExpr_ArrayDimFetch(Expr\ArrayDimFetch $node)
  {
    return $this->pDereferenceLhs($node->var)
    . '[' . (null !== $node->dim ? $this->p($node->dim) : '') . ']';
  }

  /**
   * @param Expr\ConstFetch $node
   *
   * @return string
   */
  public function pExpr_ConstFetch(Expr\ConstFetch $node)
  {
    return $this->p($node->name);
  }

  /**
   * @param Expr\ClassConstFetch $node
   *
   * @return string
   */
  public function pExpr_ClassConstFetch(Expr\ClassConstFetch $node)
  {
    return $this->p($node->class) . '::' . $node->name;
  }

  /**
   * @param Expr\PropertyFetch $node
   *
   * @return string
   */
  public function pExpr_PropertyFetch(Expr\PropertyFetch $node)
  {
    return $this->pDereferenceLhs($node->var) . '->' . $this->pObjectProperty(
      $node->name
    );
  }

  /**
   * @param Expr\StaticPropertyFetch $node
   *
   * @return string
   */
  public function pExpr_StaticPropertyFetch(Expr\StaticPropertyFetch $node)
  {
    return $this->pDereferenceLhs(
      $node->class
    ) . '::$' . $this->pObjectProperty($node->name);
  }

  /**
   * @param Expr\ShellExec $node
   *
   * @return string
   */
  public function pExpr_ShellExec(Expr\ShellExec $node)
  {
    return '`' . $this->pEncapsList($node->parts, '`') . '`';
  }

  /**
   * @param Expr\Closure $node
   *
   * @return string
   */
  public function pExpr_Closure(Expr\Closure $node)
  {
    return ($node->static ? 'static ' : '')
    . 'function ' . ($node->byRef ? '&' : '')
    . '(' . $this->pCommaSeparated($node->params) . ')'
    . (!empty($node->uses) ? ' use(' . $this->pCommaSeparated(
        $node->uses
      ) . ')' : '')
    . (null !== $node->returnType ? ' : ' . $this->pType(
        $node->returnType
      ) : '')
    . ' {' . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Expr\ClosureUse $node
   *
   * @return string
   */
  public function pExpr_ClosureUse(Expr\ClosureUse $node)
  {
    return ($node->byRef ? '&' : '') . '$' . $node->var;
  }

  /**
   * @param Expr\New_ $node
   *
   * @return string
   */
  public function pExpr_New(Expr\New_ $node)
  {
    if ($node->class instanceof Stmt\Class_)
    {
      $args = $node->args ? '(' . $this->pCommaSeparated(
          $node->args
        ) . ')' : '';

      return 'new ' . $this->pClassCommon($node->class, $args);
    }

    return 'new ' . $this->p($node->class) . '(' . $this->pCommaSeparated(
      $node->args
    ) . ')';
  }

  /**
   * @param Expr\Clone_ $node
   *
   * @return string
   */
  public function pExpr_Clone(Expr\Clone_ $node)
  {
    return 'clone ' . $this->p($node->expr);
  }

  /**
   * @param Expr\Ternary $node
   *
   * @return string
   */
  public function pExpr_Ternary(Expr\Ternary $node)
  {
    // a bit of cheating: we treat the ternary as a binary op where the ?...: part is the operator.
    // this is okay because the part between ? and : never needs parentheses.
    return $this->pInfixOp(
      'Expr_Ternary',
      $node->cond,
      ' ?' . (null !== $node->if ? ' ' . $this->p($node->if) . ' ' : '') . ': ',
      $node->else
    );
  }

  /**
   * @param Expr\Exit_ $node
   *
   * @return string
   */
  public function pExpr_Exit(Expr\Exit_ $node)
  {
    return 'die' . (null !== $node->expr ? '(' . $this->p(
        $node->expr
      ) . ')' : '');
  }

  /**
   * @param Expr\Yield_ $node
   *
   * @return string
   */
  public function pExpr_Yield(Expr\Yield_ $node)
  {
    if ($node->value === null)
    {
      return 'yield';
    }
    else
    {
      // this is a bit ugly, but currently there is no way to detect whether the parentheses are necessary
      return '(yield '
      . ($node->key !== null ? $this->p($node->key) . ' => ' : '')
      . $this->p($node->value)
      . ')';
    }
  }

  // Declarations

  /**
   * @param Stmt\Namespace_ $node
   *
   * @return string
   */
  public function pStmt_Namespace(Stmt\Namespace_ $node)
  {
    if ($this->canUseSemicolonNamespaces)
    {
      return 'namespace ' . $this->p($node->name) . ';' . "\n" . $this->pStmts(
        $node->stmts,
        false
      );
    }
    else
    {
      return 'namespace' . (null !== $node->name ? ' ' . $this->p(
          $node->name
        ) : '')
      . ' {' . $this->pStmts($node->stmts) . "\n" . '}';
    }
  }

  /**
   * @param Stmt\Use_ $node
   *
   * @return string
   */
  public function pStmt_Use(Stmt\Use_ $node)
  {
    return 'use ' . $this->pUseType($node->type)
    . $this->pCommaSeparated($node->uses) . ';';
  }

  /**
   * @param Stmt\GroupUse $node
   *
   * @return string
   */
  public function pStmt_GroupUse(Stmt\GroupUse $node)
  {
    return 'use ' . $this->pUseType($node->type) . $this->pName($node->prefix)
    . '\{' . $this->pCommaSeparated($node->uses) . '};';
  }

  /**
   * @param Stmt\UseUse $node
   *
   * @return string
   */
  public function pStmt_UseUse(Stmt\UseUse $node)
  {
    return $this->pUseType($node->type) . $this->p($node->name)
    . ($node->name->getLast() !== $node->alias ? ' as ' . $node->alias : '');
  }

  /**
   * @param $type
   *
   * @return string
   */
  private function pUseType($type)
  {
    return $type === Stmt\Use_::TYPE_FUNCTION ? 'function '
      : ($type === Stmt\Use_::TYPE_CONSTANT ? 'const ' : '');
  }

  /**
   * @param Stmt\Interface_ $node
   *
   * @return string
   */
  public function pStmt_Interface(Stmt\Interface_ $node)
  {
    return 'interface ' . $node->name
    . (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated(
        $node->extends
      ) : '')
    . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\Class_ $node
   *
   * @return string
   */
  public function pStmt_Class(Stmt\Class_ $node)
  {
    return $this->pClassCommon($node, ' ' . $node->name);
  }

  /**
   * @param Stmt\Trait_ $node
   *
   * @return string
   */
  public function pStmt_Trait(Stmt\Trait_ $node)
  {
    return 'trait ' . $node->name
    . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\TraitUse $node
   *
   * @return string
   */
  public function pStmt_TraitUse(Stmt\TraitUse $node)
  {
    return 'use ' . $this->pCommaSeparated($node->traits)
    . (empty($node->adaptations)
      ? ';'
      : ' {' . $this->pStmts($node->adaptations) . "\n" . '}');
  }

  /**
   * @param Stmt\TraitUseAdaptation\Precedence $node
   *
   * @return string
   */
  public function pStmt_TraitUseAdaptation_Precedence(
    Stmt\TraitUseAdaptation\Precedence $node
  )
  {
    return $this->p($node->trait) . '::' . $node->method
    . ' insteadof ' . $this->pCommaSeparated($node->insteadof) . ';';
  }

  /**
   * @param Stmt\TraitUseAdaptation\Alias $node
   *
   * @return string
   */
  public function pStmt_TraitUseAdaptation_Alias(
    Stmt\TraitUseAdaptation\Alias $node
  )
  {
    return (null !== $node->trait ? $this->p($node->trait) . '::' : '')
    . $node->method . ' as'
    . (null !== $node->newModifier ? ' ' . rtrim(
        $this->pModifiers($node->newModifier),
        ' '
      ) : '')
    . (null !== $node->newName ? ' ' . $node->newName : '')
    . ';';
  }

  /**
   * @param Stmt\Property $node
   *
   * @return string
   */
  public function pStmt_Property(Stmt\Property $node)
  {
    return (0 === $node->type ? 'var ' : $this->pModifiers(
      $node->type
    )) . $this->pCommaSeparated($node->props) . ';';
  }

  /**
   * @param Stmt\PropertyProperty $node
   *
   * @return string
   */
  public function pStmt_PropertyProperty(Stmt\PropertyProperty $node)
  {
    return '$' . $node->name
    . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
  }

  /**
   * @param Stmt\ClassMethod $node
   *
   * @return string
   */
  public function pStmt_ClassMethod(Stmt\ClassMethod $node)
  {
    return $this->pModifiers($node->type)
    . 'function ' . ($node->byRef ? '&' : '') . $node->name
    . '(' . $this->pCommaSeparated($node->params) . ')'
    . (null !== $node->returnType ? ' : ' . $this->pType(
        $node->returnType
      ) : '')
    . (null !== $node->stmts
      ? "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}'
      : ';');
  }

  /**
   * @param Stmt\ClassConst $node
   *
   * @return string
   */
  public function pStmt_ClassConst(Stmt\ClassConst $node)
  {
    return 'const ' . $this->pCommaSeparated($node->consts) . ';';
  }

  /**
   * @param Stmt\Function_ $node
   *
   * @return string
   */
  public function pStmt_Function(Stmt\Function_ $node)
  {
    return 'function ' . ($node->byRef ? '&' : '') . $node->name
    . '(' . $this->pCommaSeparated($node->params) . ')'
    . (null !== $node->returnType ? ' : ' . $this->pType(
        $node->returnType
      ) : '')
    . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\Const_ $node
   *
   * @return string
   */
  public function pStmt_Const(Stmt\Const_ $node)
  {
    return 'const ' . $this->pCommaSeparated($node->consts) . ';';
  }

  /**
   * @param Stmt\Declare_ $node
   *
   * @return string
   */
  public function pStmt_Declare(Stmt\Declare_ $node)
  {
    return 'declare (' . $this->pCommaSeparated($node->declares) . ')'
    . (null !== $node->stmts ? ' {' . $this->pStmts(
        $node->stmts
      ) . "\n" . '}' : ';');
  }

  /**
   * @param Stmt\DeclareDeclare $node
   *
   * @return string
   */
  public function pStmt_DeclareDeclare(Stmt\DeclareDeclare $node)
  {
    return $node->key . '=' . $this->p($node->value);
  }

  // Control flow

  /**
   * @param Stmt\If_ $node
   *
   * @return string
   */
  public function pStmt_If(Stmt\If_ $node)
  {
    return 'if (' . $this->p($node->cond) . ') ' . PHP_EOL . '{'
    . $this->pStmts($node->stmts) . "\n" . '}'
    . $this->pImplode($node->elseifs)
    . (null !== $node->else ? $this->p($node->else) : '');
  }

  /**
   * @param Stmt\ElseIf_ $node
   *
   * @return string
   */
  public function pStmt_ElseIf(Stmt\ElseIf_ $node)
  {
    return ' elseif (' . $this->p($node->cond) . ') ' . PHP_EOL . '{'
    . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\Else_ $node
   *
   * @return string
   */
  public function pStmt_Else(Stmt\Else_ $node)
  {
    return ' else ' . PHP_EOL . '{' . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\For_ $node
   *
   * @return string
   */
  public function pStmt_For(Stmt\For_ $node)
  {
    return 'for ('
    . $this->pCommaSeparated(
      $node->init
    ) . ';' . (!empty($node->cond) ? ' ' : '')
    . $this->pCommaSeparated(
      $node->cond
    ) . ';' . (!empty($node->loop) ? ' ' : '')
    . $this->pCommaSeparated($node->loop)
    . ')' . PHP_EOL . '{' . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\Foreach_ $node
   *
   * @return string
   */
  public function pStmt_Foreach(Stmt\Foreach_ $node)
  {
    return 'foreach (' . $this->p($node->expr) . ' as '
    . (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : '')
    . ($node->byRef ? '&' : '') . $this->p(
      $node->valueVar
    ) . ')' . PHP_EOL . '{'
    . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\While_ $node
   *
   * @return string
   */
  public function pStmt_While(Stmt\While_ $node)
  {
    return 'while (' . $this->p($node->cond) . ')' . PHP_EOL . '{'
    . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\Do_ $node
   *
   * @return string
   */
  public function pStmt_Do(Stmt\Do_ $node)
  {
    return 'do {' . $this->pStmts($node->stmts) . "\n"
    . '} while (' . $this->p($node->cond) . ');';
  }

  /**
   * @param Stmt\Switch_ $node
   *
   * @return string
   */
  public function pStmt_Switch(Stmt\Switch_ $node)
  {
    return 'switch (' . $this->p($node->cond) . ')' . PHP_EOL . '{'
    . $this->pStmts($node->cases) . "\n" . '}';
  }

  /**
   * @param Stmt\Case_ $node
   *
   * @return string
   */
  public function pStmt_Case(Stmt\Case_ $node)
  {
    return (null !== $node->cond ? 'case ' . $this->p(
        $node->cond
      ) : 'default') . ':'
    . $this->pStmts($node->stmts);
  }

  /**
   * @param Stmt\TryCatch $node
   *
   * @return string
   */
  public function pStmt_TryCatch(Stmt\TryCatch $node)
  {
    return 'try {' . $this->pStmts($node->stmts) . "\n" . '}'
    . $this->pImplode($node->catches)
    . ($node->finallyStmts !== null
      ? ' finally {' . $this->pStmts($node->finallyStmts) . "\n" . '}'
      : '');
  }

  /**
   * @param Stmt\Catch_ $node
   *
   * @return string
   */
  public function pStmt_Catch(Stmt\Catch_ $node)
  {
    return ' catch (' . $this->p(
      $node->type
    ) . ' $' . $node->var . ')' . PHP_EOL . '{'
    . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param Stmt\Break_ $node
   *
   * @return string
   */
  public function pStmt_Break(Stmt\Break_ $node)
  {
    return 'break' . ($node->num !== null ? ' ' . $this->p(
        $node->num
      ) : '') . ';';
  }

  /**
   * @param Stmt\Continue_ $node
   *
   * @return string
   */
  public function pStmt_Continue(Stmt\Continue_ $node)
  {
    return 'continue' . ($node->num !== null ? ' ' . $this->p(
        $node->num
      ) : '') . ';';
  }

  /**
   * @param Stmt\Return_ $node
   *
   * @return string
   */
  public function pStmt_Return(Stmt\Return_ $node)
  {
    return 'return' . (null !== $node->expr ? ' ' . $this->p(
        $node->expr
      ) : '') . ';';
  }

  /**
   * @param Stmt\Throw_ $node
   *
   * @return string
   */
  public function pStmt_Throw(Stmt\Throw_ $node)
  {
    return 'throw ' . $this->p($node->expr) . ';';
  }

  /**
   * @param Stmt\Label $node
   *
   * @return string
   */
  public function pStmt_Label(Stmt\Label $node)
  {
    return $node->name . ':';
  }

  /**
   * @param Stmt\Goto_ $node
   *
   * @return string
   */
  public function pStmt_Goto(Stmt\Goto_ $node)
  {
    return 'goto ' . $node->name . ';';
  }

  // Other

  /**
   * @param Stmt\Echo_ $node
   *
   * @return string
   */
  public function pStmt_Echo(Stmt\Echo_ $node)
  {
    return 'echo ' . $this->pCommaSeparated($node->exprs) . ';';
  }

  /**
   * @param Stmt\Static_ $node
   *
   * @return string
   */
  public function pStmt_Static(Stmt\Static_ $node)
  {
    return 'static ' . $this->pCommaSeparated($node->vars) . ';';
  }

  /**
   * @param Stmt\Global_ $node
   *
   * @return string
   */
  public function pStmt_Global(Stmt\Global_ $node)
  {
    return 'global ' . $this->pCommaSeparated($node->vars) . ';';
  }

  /**
   * @param Stmt\StaticVar $node
   *
   * @return string
   */
  public function pStmt_StaticVar(Stmt\StaticVar $node)
  {
    return '$' . $node->name
    . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
  }

  /**
   * @param Stmt\Unset_ $node
   *
   * @return string
   */
  public function pStmt_Unset(Stmt\Unset_ $node)
  {
    return 'unset(' . $this->pCommaSeparated($node->vars) . ');';
  }

  /**
   * @param Stmt\InlineHTML $node
   *
   * @return string
   */
  public function pStmt_InlineHTML(Stmt\InlineHTML $node)
  {
    return '?>' . $this->pNoIndent("\n" . $node->value) . '<?php ';
  }

  /**
   * @param Stmt\HaltCompiler $node
   *
   * @return string
   */
  public function pStmt_HaltCompiler(Stmt\HaltCompiler $node)
  {
    return '__halt_compiler();' . $node->remaining;
  }

  // Helpers

  /**
   * @param $node
   *
   * @return string
   */
  protected function pType($node)
  {
    return is_string($node) ? $node : $this->p($node);
  }

  /**
   * @param Stmt\Class_ $node
   * @param             $afterClassToken
   *
   * @return string
   */
  protected function pClassCommon(Stmt\Class_ $node, $afterClassToken)
  {
    return $this->pModifiers($node->type)
    . 'class' . $afterClassToken
    . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
    . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated(
        $node->implements
      ) : '')
    . "\n" . '{' . $this->pStmts($node->stmts) . "\n" . '}';
  }

  /**
   * @param $node
   *
   * @return string
   */
  protected function pObjectProperty($node)
  {
    if ($node instanceof Expr)
    {
      return '{' . $this->p($node) . '}';
    }
    else
    {
      return $node;
    }
  }

  /**
   * @param $modifiers
   *
   * @return string
   */
  protected function pModifiers($modifiers)
  {
    return ($modifiers & Stmt\Class_::MODIFIER_PUBLIC ? 'public ' : '')
    . ($modifiers & Stmt\Class_::MODIFIER_PROTECTED ? 'protected ' : '')
    . ($modifiers & Stmt\Class_::MODIFIER_PRIVATE ? 'private ' : '')
    . ($modifiers & Stmt\Class_::MODIFIER_STATIC ? 'static ' : '')
    . ($modifiers & Stmt\Class_::MODIFIER_ABSTRACT ? 'abstract ' : '')
    . ($modifiers & Stmt\Class_::MODIFIER_FINAL ? 'final ' : '');
  }

  /**
   * @param array $encapsList
   * @param       $quote
   *
   * @return string
   */
  protected function pEncapsList(array $encapsList, $quote)
  {
    $return = '';
    foreach ($encapsList as $element)
    {
      if ($element instanceof Scalar\EncapsedStringPart)
      {
        $return .= addcslashes($element->value, "\n\r\t\f\v$" . $quote . "\\");
      }
      else
      {
        $return .= '{' . $this->p($element) . '}';
      }
    }

    return $return;
  }

  /**
   * @param Node $node
   *
   * @return string
   */
  protected function pDereferenceLhs(Node $node)
  {
    if ($node instanceof Expr\Variable
      || $node instanceof Name
      || $node instanceof Expr\ArrayDimFetch
      || $node instanceof Expr\PropertyFetch
      || $node instanceof Expr\StaticPropertyFetch
      || $node instanceof Expr\FuncCall
      || $node instanceof Expr\MethodCall
      || $node instanceof Expr\StaticCall
      || $node instanceof Expr\Array_
      || $node instanceof Scalar\String_
      || $node instanceof Expr\ConstFetch
      || $node instanceof Expr\ClassConstFetch
    )
    {
      return $this->p($node);
    }
    else
    {
      return '(' . $this->p($node) . ')';
    }
  }

  /**
   * @param Node $node
   *
   * @return string
   */
  protected function pCallLhs(Node $node)
  {
    if ($node instanceof Name
      || $node instanceof Expr\Variable
      || $node instanceof Expr\ArrayDimFetch
      || $node instanceof Expr\FuncCall
      || $node instanceof Expr\MethodCall
      || $node instanceof Expr\StaticCall
      || $node instanceof Expr\Array_
    )
    {
      return $this->p($node);
    }
    else
    {
      return '(' . $this->p($node) . ')';
    }
  }

  /**
   * @param Stmt\Nop $node
   *
   * @return string
   */
  public function pStmt_Nop(Stmt\Nop $node)
  {
    return '';
  }
}
