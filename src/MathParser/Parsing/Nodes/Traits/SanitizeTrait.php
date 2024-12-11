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

use MathParser\Parsing\Nodes\{IntegerNode, Node, NumberNode};

/**
 * Trait for upgrading numbers (ints and floats) to NumberNode,
 * making it possible to call the Node constructors directly
 * with numbers, making the code cleaner.
 */
trait SanitizeTrait {
	/** Convert ints and floats to NumberNodes */
	protected function sanitize(null|Node|int|float $operand): ?Node {
		if (is_int($operand)) {
			return new IntegerNode($operand);
		}
		if (is_float($operand)) {
			return new NumberNode($operand);
		}

		return $operand;
	}
}
