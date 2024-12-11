<?php declare(strict_types=1);

namespace Tests\MathParser\Extensions;

use MathParser\Extensions\Math;
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase {
	public function testGcd() {
		$this->assertEquals(4, Math::gcd(8, 12));
		$this->assertEquals(4, Math::gcd(12, 8));
		$this->assertEquals(1, Math::gcd(12, 7));

		// Edge cases

		$this->assertEquals(5, Math::gcd(0, 5));
		$this->assertEquals(0, Math::gcd(0, 0));
		$this->assertEquals(-2, Math::gcd(2, -2));
		$this->assertEquals(2, Math::gcd(-2, -2));
	}

	public function testLogGamma() {
		$this->assertEqualsWithDelta(857.933_669_8, Math::logGamma(200), 3e-7, '');
		$this->assertEqualsWithDelta(log(120), Math::logGamma(6), 3e-9, '');
		$this->assertEqualsWithDelta(3.957_813_967_618_7, Math::logGamma(5.5), 3e-9, '');
	}

	public function testFactorial() {
		$this->assertEquals(1, Math::factorial(0));
		$this->assertEquals(6, Math::factorial(3));
		$this->assertEquals(362_880, Math::factorial(9));
	}

	public function testSemiFactorial() {
		$this->assertEquals(1, Math::semiFactorial(0));
		$this->assertEquals(1, Math::semiFactorial(1));
		$this->assertEquals(2, Math::semiFactorial(2));
		$this->assertEquals(3, Math::semiFactorial(3));
		$this->assertEquals(8, Math::semiFactorial(4));
		$this->assertEquals(15, Math::semiFactorial(5));
		$this->assertEquals(48, Math::semiFactorial(6));
		$this->assertEquals(105, Math::semiFactorial(7));
	}
}
