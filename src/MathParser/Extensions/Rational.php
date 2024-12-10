<?php

declare(strict_types=1);
/*
 * @package     Rational
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Extensions;

use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Exceptions\DivisionByZeroException;

/**
* Implementation of rational number arithmetic.
*
* ## Example:
* ~~~{.php}
* $a = new Rational(1, 4);           // creates the rational number 1/4
* $b = new Rational(2, 3);           // creates the rational number -1+i
* sum = Rational::add($a, $b)        // computes the sum 1/4 + 2/3
* ~~~
*
*
*/
class Rational
{
    /**
     * Constuctor for Rational number class
     *
     * $r = new Rational(2, 4)         // creates 1/2
     * $r = new Rational(2, 4, false)  // creates 2/4
     *
     * @param int $p numerator
     * @param int $q denominator
     * @param bool $normalize (default true) If true, store in normalized form,
     *             i.e. positive denominator and gcd($p, $q) = 1
     */
    public function __construct(
        public float $p,
        public float $q,
        bool $normalize=true
    ) {
        if ($q === 0 || $q === 0.0) {
            throw new DivisionByZeroException();
        }

        if ($normalize) {
            $this->normalize();
        }
    }

    /**
     * Normalize, i.e. make sure the denominator is positive and that
     * the numerator and denominator have no common factors
     */
    private function normalize(): void
    {
        $gcd = Math::gcd($this->p, $this->q);

        if ($gcd === 0) {
            throw new DivisionByZeroException();
        }

        $this->p = $this->p/$gcd;
        $this->q = $this->q/$gcd;

        if ($this->q < 0) {
            $this->p = -$this->p;
            $this->q = -$this->q;
        }
    }

    /**
     * Add two rational numbers
     *
     * Rational::add($x, $y) computes and returns $x+$y
     */
    public static function add(string|int|float|Rational $x, string|int|float|Rational $y): Rational
    {
        $xRat = static::parse($x);
        $yRat = static::parse($y);

        $resp = $xRat->p * $yRat->q + $xRat->q * $yRat->p;
        $resq = $xRat->q * $yRat->q;

        return new Rational($resp, $resq);
    }

    /**
     * Subtract two rational numbers
     *
     * Rational::sub($x, $y) computes and returns $x-$y
     */
    public static function sub(string|int|float|Rational $x, string|int|float|Rational $y): Rational
    {
        $xRat = static::parse($x);
        $yRat = static::parse($y);

        $resp = $xRat->p * $yRat->q - $xRat->q * $yRat->p;
        $resq = $xRat->q * $yRat->q;

        return new Rational($resp, $resq);
    }

    /**
     * Multiply two rational numbers
     *
     * Rational::mul($x, $y) computes and returns $x*$y
     */
    public static function mul(string|int|float|Rational $x, string|int|float|Rational $y): Rational
    {
        $xRat = static::parse($x);
        $yRat = static::parse($y);

        $resp = $xRat->p * $yRat->p;
        $resq = $xRat->q * $yRat->q;

        return new Rational($resp, $resq);
    }

    /**
     * Divide two rational numbers
     *
     * Rational::div($x, $y) computes and returns $x/$y
     */
    public static function div(string|int|float|Rational $x, string|int|float|Rational $y): Rational
    {
        $xRat = static::parse($x);
        $yRat = static::parse($y);

        if ($yRat->p == 0) {
            throw new DivisionByZeroException();
        }

        $resp = $xRat->p * $yRat->q;
        $resq = $xRat->q * $yRat->p;

        return new Rational($resp, $resq);
    }

    /**
     * convert rational number to string, adding a '+' if the number is positive
     */
    public function signed(): string
    {
        if ($this->q == 1) {
            return sprintf("%+d", $this->p);
        }
        return sprintf("%+d/%d", $this->p, $this->q);
    }

    /**
     * Test whether a string represents an positive integer
     */
    private static function isInteger(string $value): bool
    {
        return ctype_digit($value);
    }

    /**
     * Test whether a string represents a signed integer
     */
    private static function isSignedInteger($value): bool
    {
        return is_int(filter_var($value, FILTER_VALIDATE_INT));
    }

    /**
     * Test if the rational number is NAN
     */
    public function is_nan(): bool
    {
        if ($this->q === 0 || $this->q === 0.0) {
            return true;
        }
        return is_nan($this->p) || is_nan($this->q);
    }

    /**
     * Convert $value to Rational
     *
     * @throws SyntaxErrorException
     */
    public static function parse(string|int|float|Rational $value, bool $normalize=true): Rational
    {
        if ($value instanceof Rational) {
            return $value;
        }
        if ($value === '') {
            return null;
        }
        if ($value === 'NAN') {
            return new Rational(NAN, 1);
        }
        if ($value === 'INF') {
            return new Rational(INF, 1);
        }
        if ($value === '-INF') {
            return new Rational(-INF, 1);
        }

        $data = $value;

        $numbers = explode('/', $data);
        if (count($numbers) === 1) {
            $p = static::isSignedInteger($numbers[0]) ? intval($numbers[0]) : NAN;
            $q = 1;
        } elseif (count($numbers) !== 2) {
            $p = NAN;
            $q = NAN;
        } else {
            $p = static::isSignedInteger($numbers[0]) ? intval($numbers[0]) : NAN;
            $q = ctype_digit($numbers[1]) ? intval($numbers[1]) : NAN;
        }

        if (is_nan($p) || is_nan($q)) {
            throw new SyntaxErrorException();
        }


        return new Rational($p, $q, $normalize);
    }

    /**
     * convert float to Rational
     *
     * Convert float to a continued fraction, with prescribed accuracy
     */
    public static function fromFloat(string|int|float $float, float $tolerance=1e-7): Rational
    {
        if (is_string($float) && preg_match('~^\-?\d+([,|.]\d+)?$~', $float)) {
            $float = floatval(str_replace(',', '.', $float));
        }
        if (is_int($float)) {
            $float = (float)$float;
        }

        if ($float === 0.0) {
            return new Rational(0, 1);
        }
        $negative = ($float < 0);
        if ($negative) {
            $float = abs($float);
        }
        $num1 = 1;
        $num2 = 0;
        $den1 = 0;
        $den2 = 1;
        $oneOver = 1 / $float;
        do {
            $oneOver = 1 / $oneOver;
            $floor = floor($oneOver);
            $aux = $num1;
            $num1 = $floor * $num1 + $num2;
            $num2 = $aux;
            $aux = $den1;
            $den1 = $floor * $den1 + $den2;
            $den2 = $aux;
            $oneOver = $oneOver - $floor;
        } while (abs($float - $num1 / $den1) > $float * $tolerance);
        if ($negative) {
            $num1 *= -1;
        }

        return new Rational(intval($num1), intval($den1));
    }

    /**
     * Convert Rational to string
     */
    public function __toString(): string
    {
        if ($this->q === 1 || $this->q === 1.0) {
            return "$this->p";
        }
        return "$this->p/$this->q";
    }
}
