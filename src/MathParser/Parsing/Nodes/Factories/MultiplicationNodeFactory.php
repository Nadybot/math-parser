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

use MathParser\Parsing\Nodes\Traits\{Numeric, Sanitize};
use MathParser\Parsing\Nodes\{ExpressionNode, IntegerNode, Node, NumberNode, NumericNode, RationalNode};

/**
 * Factory for creating an ExpressionNode representing '*'.
 *
 * Some basic simplification is applied to the resulting Node.
 */
class MultiplicationNodeFactory implements ExpressionNodeFactory {
	use Sanitize;
	use Numeric;

	/**
	 * Create a Node representing 'leftOperand * rightOperand'
	 *
	 * Using some simplification rules, create a NumberNode or ExpressionNode
	 * giving an AST correctly representing 'leftOperand * rightOperand'.
	 *
	 * ### Simplification rules:
	 *
	 * - To simplify the use of the function, convert integer params to NumberNodes
	 * - If $leftOperand and $rightOperand are both NumberNodes, return a single NumberNode containing their product
	 * - If $leftOperand or $rightOperand is a NumberNode representing 0, return '0'
	 * - If $leftOperand or $rightOperand is a NumberNode representing 1, return the other factor
	 *
	 * @param Node|int|float $leftOperand  First factor
	 * @param Node|int|float $rightOperand Second factor
	 */
	public function makeNode(int|float|Node $leftOperand, int|float|Node $rightOperand): Node {
		$leftOperand = $this->sanitize($leftOperand);
		$rightOperand = $this->sanitize($rightOperand);

		$node = $this->numericFactors($leftOperand, $rightOperand);
		if ($node) {
			return $node;
		}

		return new ExpressionNode($leftOperand, '*', $rightOperand);
	}

	/** Simplify a*b when a or b are certain numeric values */
	private function numericFactors(Node $leftOperand, Node $rightOperand): ?Node {
		if ($rightOperand instanceof NumericNode) {
			if ((float)$rightOperand->getValue() === 0.0) {
				return new IntegerNode(0);
			}
			if ((float)$rightOperand->getValue() === 1.0) {
				return $leftOperand;
			}
		}
		if ($leftOperand instanceof NumericNode) {
			if ((float)$leftOperand->getValue() === 0.0) {
				return new IntegerNode(0);
			}
			if ((float)$leftOperand->getValue() === 1.0) {
				return $rightOperand;
			}
		}

		if (!($leftOperand instanceof NumericNode) || !($rightOperand instanceof NumericNode)) {
			return null;
		}
		$type = $this->resultingType($leftOperand, $rightOperand);

		switch ($type) {
			case Node::NUMERIC_FLOAT:
				return new NumberNode($leftOperand->getValue() * $rightOperand->getValue());

			case Node::NUMERIC_RATIONAL:
				assert($leftOperand instanceof RationalNode);
				assert($rightOperand instanceof RationalNode);
				$p = $leftOperand->getNumerator() * $rightOperand->getNumerator();
				$q = $leftOperand->getDenominator() * $rightOperand->getDenominator();
				return new RationalNode($p, $q);

			case Node::NUMERIC_INTEGER:
				assert($leftOperand instanceof IntegerNode);
				assert($rightOperand instanceof IntegerNode);
				return new IntegerNode($leftOperand->getValue() * $rightOperand->getValue());
		}

		return null;
	}
}
