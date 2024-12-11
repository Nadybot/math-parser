<?php

declare(strict_types=1);
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

/**
 * @namespace MathParser::Parsing::Nodes::Interfaces
 *
 * Interfaces for Nodes, in particular Node factories.
 */

namespace MathParser\Parsing\Nodes\Interfaces;

use MathParser\Parsing\Nodes\Node;

/**
 * Interface for construction of ExpressionNode, the
 * implementations of the interface, usually involves
 * some simplification of the operands.
 */
interface ExpressionNodeFactoryInterface {
	/** Factory method to create an ExpressionNode with given operands. */
	public function makeNode(int|Node $leftOperand, int|Node $rightOperand): Node;
}
