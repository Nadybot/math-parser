<?php

declare(strict_types=1);
/*
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2016 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*/

namespace MathParser\Extensions;

use InvalidArgumentException;

class Math {
	/**
	 * Compute greatest common denominator, using the Euclidean algorithm
	 *
	 * Compute and return gcd($a, $b)
	 */
	public static function gcd(int $a, int $b): int {
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
		return $sign * abs($a);
	}

	/**
	 * Compute log(Gamma($a)) where $a is a positive real number
	 *
	 * For large values of $a ($a > 171), use Stirling asympotic expansion,
	 * otherwise use the Lanczos approximation
	 *
	 * @throws InvalidArgumentException if $a < 0
	 */
	public static function logGamma(float $a): float {
		if ($a < 0) {
			throw new InvalidArgumentException('Log gamma calls should be >0.');
		}

		if ($a >= 171) {	// Lanczos approximation w/ the given coefficients is accurate to 15 digits for 0 <= real(z) <= 171
			return self::logStirlingApproximation($a);
		}
		return log(self::lanczosApproximation($a));
	}

	/**
	 * Compute factorial n! for an integer $n using iteration
	 *
	 * @throws InvalidArgumentException if $num < 0
	 */
	public static function factorial(float $num): float {
		if ($num < 0) {
			throw new InvalidArgumentException('Fatorial calls should be >0.');
		}

		$rval = 1;
		for ($i = 1; $i <= $num; $i++) {
			$rval = $rval * $i;
		}
		return $rval;
	}

	/**
	 * Compute semi-factorial n!! for an integer $n using iteration
	 *
	 * @throws InvalidArgumentException if $num < 0
	 */
	public static function semiFactorial(float $num): float {
		if ($num < 0) {
			throw new \InvalidArgumentException('Semifactorial calls should be >0.');
		}

		$rval = 1;
		while ($num >= 2) {
			$rval =$rval * $num;
			$num = $num-2;
		}
		return $rval;
	}

	/** Compute log(Gamma($x)) using Stirling asympotic expansion */
	private static function logStirlingApproximation(float $x): float {
		$t = 0.5*log(2*\M_PI) - 0.5*log($x) + $x*(log($x))-$x;

		$x2 = $x * $x;
		$x3 = $x2 * $x;
		$x4 = $x3 * $x;

		$err_term = log(1 + (1.0/(12*$x)) + (1.0/(288*$x2)) - (139.0/(51_840*$x3))
			- (571.0/(2_488_320*$x4)));

		$res = $t + $err_term;
		return $res;
	}

	/** Compute log(Gamma($x)) using Lanczos approximation */
	private static function lanczosApproximation(float $x): float {
		$g = 7;
		$p = [0.999_999_999_999_809_93, 676.520_368_121_885_1, -1_259.139_216_722_402_8,
			771.323_428_777_653_13, -176.615_029_162_140_59, 12.507_343_278_686_905,
			-0.138_571_095_265_720_12, 9.984_369_578_019_571_6e-6, 1.505_632_735_149_311_6e-7];

		if (abs($x - floor($x)) < 1e-16) {
			// if we're real close to an integer, let's just compute the factorial integerly.

			if ($x >= 1) {
				return self::factorial($x - 1);
			}
			return \INF;
		}
		$x -= 1;

		$y = $p[0];

		for ($i=1; $i < $g+2; $i++) {
			$y = $y + $p[$i]/($x + $i);
		}
		$t = $x + $g + 0.5;


		$res_fr = sqrt(2*\M_PI) * exp((($x+0.5)*log($t))-$t)*$y;

		return $res_fr;
	}
}
