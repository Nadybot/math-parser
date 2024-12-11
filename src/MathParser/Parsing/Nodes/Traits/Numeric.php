<?php

declare(strict_types=1);
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

/** @namespace MathParser::Parsing::Nodes::Traits
 *
 * Traits for Nodes
 */

namespace MathParser\Parsing\Nodes\Traits;

use MathParser\Parsing\Nodes\{IntegerNode, Node, NumberNode, NumericNode, RationalNode};

/**
 * Trait for upgrading numbers (ints and floats) to NumberNode,
 * making it possible to call the Node constructors directly
 * with numbers, making the code cleaner.
 */
trait Numeric {
	protected function isNumeric(?Node $operand): bool {
		return $operand instanceof NumericNode;
	}

	protected function orderType(?Node $node): int {
		if ($node instanceof IntegerNode) {
			return Node::NUMERIC_INTEGER;
		}
		if ($node instanceof RationalNode) {
			return Node::NUMERIC_RATIONAL;
		}
		if ($node instanceof NumberNode) {
			return Node::NUMERIC_FLOAT;
		}

		return 0;
	}

	protected function resultingType(NumericNode $node, NumericNode $other): int {
		return max($this->orderType($node), $this->orderType($other));
	}
}
