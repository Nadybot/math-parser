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
 * AST node representing a number (int or float)
 */
class IntegerNode extends NumericNode {
	/** The value of the represented number. */
	private int $value;

	/** Create a NumberNode with given value. */
	public function __construct(int $value) {
		$this->value = $value;
	}

	/** Returns the value */
	public function getValue(): int {
		return $this->value;
	}

	public function getNumerator(): int {
		return $this->value;
	}

	public function getDenominator(): int {
		return 1;
	}

	/** Implementing the Visitable interface. */
	public function accept(VisitorInterface $visitor): mixed {
		return $visitor->visitIntegerNode($this);
	}

	/** Implementing the compareTo abstract method. */
	public function compareTo(?Node $other): bool {
		if ($other === null) {
			return false;
		}
		if ($other instanceof RationalNode) {
			return $other->getDenominator() === 1.0 && (float)$this->getValue() === $other->getNumerator();
		}
		if (!($other instanceof IntegerNode)) {
			return false;
		}

		return $this->getValue() === $other->getValue();
	}
}
