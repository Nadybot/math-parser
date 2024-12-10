<?php

declare(strict_types=1);
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

/** @namespace MathParser::Parsing::Nodes::Factories
 *
 * Classes implementing the ExpressionNodeFactory interfaces,
 * and related functionality.
 *
 */

namespace MathParser\Parsing\Nodes\Factories;

use MathParser\Parsing\Nodes\ExpressionNode;
use MathParser\Parsing\Nodes\Node;

/**
 * Helper class for creating ExpressionNodes.
 *
 * Wrapper class, setting up factories for creating ExpressionNodes
 * of various types (one for each operator). These factories take
 * case of basic simplification.
 *
 * ### Examples
 *
 * ~~~{.php}
 * use MathParser\Parsing\Nodes\Factories\NodeFactory;
 *
 * $factory = new NodeFactory();
 * // Create AST for 'x/y + x*y'
 * $node = $factory->addition(
 *      $factory->division(new VariableNode('x'), new VariableNode('y')),
 *      $factory->multiplication(new VariableNode('x'), new VariableNode('y'))
 * );
 * ~~~
 */
class NodeFactory
{
    /**
     * Factory for creating addition nodes
     **/
    protected AdditionNodeFactory $additionFactory;

    /**
     * Factory for creating subtraction nodes (including unary minus)
     **/
    protected SubtractionNodeFactory $subtractionFactory;

    /**
     * Factory for creating multiplication nodes
     **/
    protected MultiplicationNodeFactory $multiplicationFactory;

    /**
     * Factory for creating division nodes
     **/
    protected DivisionNodeFactory $divisionFactory;

    /**
     * Factory for creating exponentiation nodes
     **/
    protected ExponentiationNodeFactory $exponentiationFactory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->additionFactory = new AdditionNodeFactory();
        $this->subtractionFactory = new SubtractionNodeFactory();
        $this->multiplicationFactory = new MultiplicationNodeFactory();
        $this->divisionFactory = new DivisionNodeFactory();
        $this->exponentiationFactory = new ExponentiationNodeFactory();
    }

    /**
     * Create an addition node representing '$leftOperand + $rightOperand'.
     */
    public function addition(Node|int $leftOperand, Node|int $rightOperand): Node
    {
        return $this->additionFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a subtraction node representing '$leftOperand - $rightOperand'.
     *
     */
    public function subtraction(Node|int $leftOperand, Node|int|null $rightOperand): Node
    {
        return $this->subtractionFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a multiplication node representing '$leftOperand * $rightOperand'.
     */
    public function multiplication(Node|int $leftOperand, Node|int $rightOperand): Node
    {
        return $this->multiplicationFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a division node representing '$leftOperand / $rightOperand'.
     */
    public function division(Node|int $leftOperand, Node|int $rightOperand): Node
    {
        return $this->divisionFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create an exponentiation node representing '$leftOperand ^ $rightOperand'.
     */
    public function exponentiation(Node|int $leftOperand, Node|int $rightOperand): Node
    {
        return $this->exponentiationFactory->makeNode($leftOperand, $rightOperand);
    }

    /**
     * Create a unary minus node representing '-$operand'.
     */
    public function unaryMinus(Node|int|float $operand): Node
    {
        return $this->subtractionFactory->createUnaryMinusNode($operand);
    }

    /**
     * Simplify the given ExpressionNode, using the appropriate factory.
     *
     * @return Node Simplified version of the input
     */
    public function simplify(ExpressionNode $node): Node
    {
        return match ($node->getOperator()) {
            '+' => $this->addition($node->getLeft(), $node->getRight()),
            '-' => $this->subtraction($node->getLeft(), $node->getRight()),
            '*' => $this->multiplication($node->getLeft(), $node->getRight()),
            '/' => $this->division($node->getLeft(), $node->getRight()),
            '^' => $this->exponentiation($node->getLeft(), $node->getRight()),
        };
    }
}
