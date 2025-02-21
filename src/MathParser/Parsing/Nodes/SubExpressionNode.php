<?php

declare(strict_types=1);
/*
 * @package     Parsing
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Parsing\Nodes;

use MathParser\Interpreting\Visitors\VisitorInterface;

/**
 * AST node representing a subexpression. Only for temporary
 * use in the parser.
 */
class SubExpressionNode extends Node {
	/**
	 * Create a SubExpressionNode with given value.
	 *
	 * @param string $value Dummy value, usually '('. A temporary SubExpressionNode
	 *                      is generated by the parser when encountering a parenthesized subexpression.
	 */
	public function __construct(
		private string $value
	) {
	}

	/** Returns the value */
	public function getValue(): string {
		return $this->value;
	}

	public function getOperator(): string {
		return '(';
	}

	/** Implementing the Visitable interface. */
	public function accept(VisitorInterface $visitor): mixed {
		return null;
	}

	/** Implementing the compareTo abstract method. */
	public function compareTo(?Node $other): bool {
		if ($other === null) {
			return false;
		}
		if (!($other instanceof SubExpressionNode)) {
			return false;
		}

		return $this->getValue() === $other->getValue();
	}
}
