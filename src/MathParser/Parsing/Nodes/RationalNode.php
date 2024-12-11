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

use MathParser\Exceptions\DivisionByZeroException;
use MathParser\Interpreting\Visitors\VisitorInterface;

/**
 * AST node representing a number (int or float)
 */
class RationalNode extends NumericNode {
	/** The numerator of the represented number. */
	private float $p;

	/** The denominator of the represented number. */
	private float $q;

	/** Create a RationalNode with given value. */
	public function __construct(float $p, float $q, bool $normalize=true) {
		if ($q === -0.0 || $q === 0.0) {
			throw new DivisionByZeroException();
		}

		$this->p = $p;
		$this->q = $q;

		if ($normalize) {
			$this->normalize();
		}
	}

	/** Returns the value */
	public function getValue(): float {
		return (1.0 * $this->p) / $this->q;
	}

	public function getNumerator(): float {
		return $this->p;
	}

	public function getDenominator(): float {
		return $this->q;
	}

	/** Implementing the Visitable interface. */
	public function accept(VisitorInterface $visitor): mixed {
		return $visitor->visitRationalNode($this);
	}

	/** Implementing the compareTo abstract method. */
	public function compareTo(?Node $other): bool {
		if ($other === null) {
			return false;
		}
		if ($other instanceof IntegerNode) {
			return $this->getDenominator() === 1.0 && $this->getNumerator() === (float)$other->getValue();
		}
		if (!($other instanceof RationalNode)) {
			return false;
		}

		return $this->getNumerator() === $other->getNumerator() && $this->getDenominator() === $other->getDenominator();
	}

	private function normalize(): void {
		if (is_nan($this->p) || is_nan($this->q)) {
			return;
		}
		$a = (int)$this->p;
		$pDigits = strrpos((string)$this->p, '.');
		if ($pDigits !== false) {
			$a = (int)($this->p * pow(10, $pDigits));
		}

		$b = (int)$this->q;
		$qDigits = strrpos((string)$this->q, '.');
		if ($qDigits !== false) {
			$b = (int)($this->q * pow(10, $qDigits));
		}
		$p = $a;
		$q = $b;

		$sign = 1;
		if ($a < 0) {
			$sign = -$sign;
		}

		if ($b < 0) {
			$sign = -$sign;
		}
		while ($b !== 0) {
			$m = $a % $b;
			$a = $b;
			$b = $m;
		}

		$gcd = $a;
		$this->p = intdiv($p, $gcd);
		$this->q = intdiv($q, $gcd);

		if ($this->q < 0) {
			$this->q = -$this->q;
			$this->p = -$this->p;
		}
	}
}
