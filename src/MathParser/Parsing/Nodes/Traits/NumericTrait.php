<?php

declare(strict_types=1);
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

namespace MathParser\Parsing\Nodes\Traits;

use MathParser\Parsing\Nodes\{IntegerNode, Node, NodeOrder, NumberNode, NumericNode, RationalNode};

/**
 * Trait for upgrading numbers (ints and floats) to NumberNode,
 * making it possible to call the Node constructors directly
 * with numbers, making the code cleaner.
 */
trait NumericTrait {
	protected function isNumeric(?Node $operand): bool {
		return $operand instanceof NumericNode;
	}

	protected function orderType(?Node $node): NodeOrder {
		if ($node instanceof IntegerNode) {
			return NodeOrder::Integer;
		}
		if ($node instanceof RationalNode) {
			return NodeOrder::Rational;
		}
		if ($node instanceof NumberNode) {
			return NodeOrder::Float;
		}

		return NodeOrder::None;
	}

	protected function resultingType(NumericNode $node, NumericNode $other): NodeOrder {
		return NodeOrder::from(max($this->orderType($node)->value, $this->orderType($other)->value));
	}
}
