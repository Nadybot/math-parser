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

use MathParser\Exceptions\DivisionByZeroException;

use MathParser\Parsing\Nodes\Interfaces\ExpressionNodeFactoryInterface;
use MathParser\Parsing\Nodes\Traits\{NumericTrait, SanitizeTrait};
use MathParser\Parsing\Nodes\{ExpressionNode, IntegerNode, Node, NodeOrder, NumberNode, NumericNode};

/**
 * Factory for creating an ExpressionNode representing '^'.
 *
 * Some basic simplification is applied to the resulting Node.
 */
class ExponentiationNodeFactory implements ExpressionNodeFactoryInterface {
	use SanitizeTrait;
	use NumericTrait;

	/**
	 * Create a Node representing '$leftOperand^$rightOperand'
	 *
	 * Using some simplification rules, create a NumberNode or ExpressionNode
	 * giving an AST correctly representing '$leftOperand^$rightOperand'.
	 *
	 * ### Simplification rules:
	 *
	 * - To simplify the use of the function, convert integer params to NumberNodes
	 * - If $leftOperand and $rightOperand are both NumberNodes, return a single NumberNode containing x^y
	 * - If $rightOperand is a NumberNode representing 0, return '1'
	 * - If $rightOperand is a NumberNode representing 1, return $leftOperand
	 * - If $leftOperand is already a power x=a^b and $rightOperand is a NumberNode, return a^(b*y)
	 *
	 * @param Node|int|float $leftOperand  Minuend
	 * @param Node|int|float $rightOperand Subtrahend
	 */
	public function makeNode(int|float|Node $leftOperand, int|float|Node $rightOperand): Node {
		$leftOperand = $this->sanitize($leftOperand);
		$rightOperand = $this->sanitize($rightOperand);

		// Simplification if the exponent is a number.
		if ($rightOperand instanceof NumericNode) {
			$node = $this->numericExponent($leftOperand, $rightOperand);
			if ($node) {
				return $node;
			}
		}

		$node = $this->doubleExponentiation($leftOperand, $rightOperand);
		if ($node) {
			return $node;
		}

		return new ExpressionNode($leftOperand, '^', $rightOperand);
	}

	/** Simplify an expression x^y, when y is numeric. */
	private function numericExponent(Node $leftOperand, NumericNode $rightOperand): ?Node {
		// 0^0 throws an exception
		if (($leftOperand instanceof NumericNode) && $this->isNumeric($rightOperand)) {
			if ((float)$leftOperand->getValue() === 0.0 && (float)$rightOperand->getValue() === 0.0) {
				throw new DivisionByZeroException();
			}
		}

		// x^0 = 1
		if ((float)$rightOperand->getValue() === 0.0) {
			return new IntegerNode(1);
		}
		// x^1 = x
		if ((float)$rightOperand->getValue() === 1.0) {
			return $leftOperand;
		}

		if (!($leftOperand instanceof NumericNode)) {
			return null;
		}
		$type = $this->resultingType($leftOperand, $rightOperand);

		// Compute x^y if both are numbers.
		switch ($type) {
			case NodeOrder::Float:
				return new NumberNode(pow($leftOperand->getValue(), $rightOperand->getValue()));

			case NodeOrder::Integer:
				assert($leftOperand instanceof IntegerNode);
				assert($rightOperand instanceof IntegerNode);
				if ($rightOperand->getValue() > 0) {
					return new IntegerNode(pow($leftOperand->getValue(), $rightOperand->getValue()));
				}
		}

		// No simplification found
		return null;
	}

	/** Simplify (x^a)^b when a and b are both numeric. */
	private function doubleExponentiation(Node $leftOperand, Node $rightOperand): ?Node {
		// (x^a)^b -> x^(ab) for a, b numbers
		if ($leftOperand instanceof ExpressionNode && $leftOperand->getOperator() === '^') {
			$factory = new MultiplicationNodeFactory();
			$power = $factory->makeNode($leftOperand->getRight(), $rightOperand);

			$base = $leftOperand->getLeft();
			return self::makeNode($base, $power);
		}

		// No simplification found
		return null;
	}
}
