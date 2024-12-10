<?php

declare(strict_types=1);
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

namespace MathParser\Parsing\Nodes\Factories;

use MathParser\Parsing\Nodes\Interfaces\ExpressionNodeFactory;

use MathParser\Parsing\Nodes\Node;
use MathParser\Parsing\Nodes\NumberNode;
use MathParser\Parsing\Nodes\IntegerNode;
use MathParser\Parsing\Nodes\RationalNode;

use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\NumericNode;
use MathParser\Parsing\Nodes\Traits\Sanitize;
use MathParser\Parsing\Nodes\Traits\Numeric;

/**
* Factory for creating an ExpressionNode representing '+'.
*
* Some basic simplification is applied to the resulting Node.
*
*/
class AdditionNodeFactory implements ExpressionNodeFactory
{
    use Sanitize;
    use Numeric;

    /**
    * Create a Node representing 'leftOperand + rightOperand'
    *
    * Using some simplification rules, create a NumberNode or ExpressionNode
    * giving an AST correctly representing 'leftOperand + rightOperand'.
    *
    * ### Simplification rules:
    *
    * - To simplify the use of the function, convert integer or float params to NumberNodes
    * - If $leftOperand and $rightOperand are both NumberNodes, return a single NumberNode containing their sum
    * - If $leftOperand or $rightOperand are NumberNodes representing 0, return the other term unchanged
    *
    * @param Node|int $leftOperand First term
    * @param Node|int $rightOperand Second term
    */
    public function makeNode(Node|int $leftOperand, Node|int $rightOperand): Node
    {
        $leftOperand = $this->sanitize($leftOperand);
        $rightOperand = $this->sanitize($rightOperand);

        $node = $this->numericTerms($leftOperand, $rightOperand);
        if ($node) {
            return $node;
        }

        return new ExpressionNode($leftOperand, '+', $rightOperand);
    }

    /** Simplify addition node when operands are numeric */
    protected function numericTerms(Node $leftOperand, Node $rightOperand): ?Node
    {
        if (($leftOperand instanceof NumericNode) && $leftOperand->getValue() == 0) {
            return $rightOperand;
        }
        if (($rightOperand instanceof NumericNode) && $rightOperand->getValue() == 0) {
            return $leftOperand;
        }

        if (!($leftOperand instanceof NumericNode) || !($rightOperand instanceof NumericNode)) {
            return null;
        }
        $type = $this->resultingType($leftOperand, $rightOperand);

        switch ($type) {
            case Node::NumericFloat:
                return new NumberNode($leftOperand->getValue() + $rightOperand->getValue());

            case Node::NumericRational:
                assert($leftOperand instanceof RationalNode);
                assert($rightOperand instanceof RationalNode);
                $p = $leftOperand->getNumerator() * $rightOperand->getDenominator() + $leftOperand->getDenominator() * $rightOperand->getNumerator();
                $q = $leftOperand->getDenominator() * $rightOperand->getDenominator();
                return new RationalNode($p, $q);

            case Node::NumericInteger:
                return new IntegerNode($leftOperand->getValue() + $rightOperand->getValue());
        }


        return null;
    }
}
