<?php declare(strict_types=1);

namespace Tests\MathParser\Extensions;

use MathParser\Exceptions\SyntaxErrorException;
use MathParser\Extensions\Complex;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase {
	public function testComplexFromString() {
		$z = Complex::parse('2+5i');
		$this->assertEquals(2, $z->r());
		$this->assertEquals(5, $z->i());
		$this->assertEquals('2+5i', "{$z}");

		// Imaginary part missing coefficient

		$z = Complex::parse('2+i');
		$this->assertEquals(2, $z->r());
		$this->assertEquals(1, $z->i());
		$this->assertEquals('2+i', "{$z}");

		$z = Complex::parse('2-i');
		$this->assertEquals(2, $z->r());
		$this->assertEquals(-1, $z->i());
		$this->assertEquals('2-i', "{$z}");

		// Real part missing

		$z = Complex::parse('i');
		$this->assertEquals(0, $z->r());
		$this->assertEquals(1, $z->i());
		$this->assertEquals('i', "{$z}");

		$z = Complex::parse('-i');
		$this->assertEquals(0, $z->r());
		$this->assertEquals(-1, $z->i());
		$this->assertEquals('-i', "{$z}");

		// Purely imaginary
		$z = Complex::parse('2i');
		$this->assertEquals(0, $z->r());
		$this->assertEquals(2, $z->i());
		$this->assertEquals('2i', "{$z}");

		$z = Complex::parse('-3i');
		$this->assertEquals(0, $z->r());
		$this->assertEquals(-3, $z->i());
		$this->assertEquals('-3i', "{$z}");

		// Imaginary part missing

		$z = Complex::parse('2');
		$this->assertEquals(2, $z->r());
		$this->assertEquals(0, $z->i());
		$this->assertEquals('2', "{$z}");

		// Rational coefficients

		$z = Complex::parse('2/3+1/2i');
		$this->assertEquals(2 / 3, $z->r());
		$this->assertEquals(1 / 2, $z->i());
		$this->assertEquals('2/3+1/2i', "{$z}");

		// Real coefficients, (note that numbers that can be identified with small fractions are printed as such)
		$z = Complex::parse('0.7-0.2i');
		$this->assertEquals(0.7, $z->r());
		$this->assertEquals(-0.2, $z->i());
		$this->assertEquals('7/10-1/5i', "{$z}");

		// Imaginary part 1 or -1
		$z = Complex::parse('4+i');
		$this->assertEquals(4, $z->r());
		$this->assertEquals(1, $z->i());
		$this->assertEquals('4+i', "{$z}");

		$z = Complex::parse('4-i');
		$this->assertEquals(4, $z->r());
		$this->assertEquals(-1, $z->i());
		$this->assertEquals('4-i', "{$z}");

		$z = Complex::parse('0.2353578');
		$this->assertEquals(0.235_357_8, $z->r());
		$this->assertEquals(0, $z->i());
	}

	public function testCreateComplex() {
		$z = Complex::create(1, 2);
		$this->assertEquals(1, $z->r());
		$this->assertEquals(2, $z->i());

		$z = Complex::create('1', '1/2');
		$this->assertEquals(1, $z->r());
		$this->assertEquals(0.5, $z->i());
	}

	public function testParseFailure() {
		$this->expectException(SyntaxErrorException::class);
		$z = Complex::parse('sdf');
	}

	public function testCanDoAritmethic() {
		$z = new Complex(1, 2);
		$w = new Complex(2, -1);

		$this->assertEquals(new Complex(3, 1), Complex::add($z, $w));
		$this->assertEquals(new Complex(-1, 3), Complex::sub($z, $w));
		$this->assertEquals(new Complex(4, 3), Complex::mul($z, $w));
		$this->assertEquals(new Complex(0, 1), Complex::div($z, $w));
	}

	public function testCanComputePowers() {
		$accuracy = 1e-9;
		$z = new Complex(1, 2);

		$this->assertEquals(new Complex(-3, 4), Complex::pow($z, 2));
		$this->assertEquals(new Complex(-11, -2), Complex::pow($z, 3));
		$this->assertEquals(new Complex(1 / 5, -2 / 5), Complex::pow($z, -1));
		$this->assertEqualsWithDelta(new Complex(0.229_140_186_0, 0.238_170_115_1), Complex::pow($z, new Complex(0, 1)), $accuracy);
	}

	public function testCanComputeTranscendentals() {
		$z = new Complex(1, 2);
		$accuracy = 1e-9;

		$this->assertEqualsWithDelta(new Complex(1.272_019_650, 0.786_151_377_8), Complex::sqrt($z), $accuracy, 'sqrt');
		$this->assertEqualsWithDelta(new Complex(3.165_778_513, 1.959_601_041), Complex::sin($z), $accuracy, 'sin');
		$this->assertEqualsWithDelta(new Complex(2.032_723_007, -3.051_897_799), Complex::cos($z), $accuracy, 'cos');
		$this->assertEqualsWithDelta(new Complex(0.033_812_826_08, 1.014_793_616), Complex::tan($z), $accuracy, 'tan');
		$this->assertEqualsWithDelta(new Complex(0.032_797_755_53, -0.984_329_226_5), Complex::cot($z), $accuracy, 'cot');
		$this->assertEqualsWithDelta(new Complex(0.427_078_586_4, 1.528_570_919), Complex::arcsin($z), $accuracy, 'arcsin');
		$this->assertEqualsWithDelta(new Complex(1.143_717_740, -1.528_570_919), Complex::arccos($z), $accuracy, 'arccos');
		$this->assertEqualsWithDelta(new Complex(1.338_972_522, 0.402_359_478_1), Complex::arctan($z), $accuracy, 'arctan');
		$this->assertEqualsWithDelta(new Complex(0.231_823_804_5, -0.402_359_478_1), Complex::arccot($z), $accuracy, 'arccot');
		$this->assertEqualsWithDelta(new Complex(-1.131_204_384, 2.471_726_672), Complex::exp($z), $accuracy, 'exp');
		$this->assertEqualsWithDelta(new Complex(0.804_718_956_2, 1.107_148_718), Complex::log($z), $accuracy, 'log');
		$this->assertEqualsWithDelta(new Complex(-0.489_056_259_0, 1.403_119_251), Complex::sinh($z), $accuracy, 'sinh');
		$this->assertEqualsWithDelta(new Complex(-0.642_148_124_7, 1.068_607_421), Complex::cosh($z), $accuracy, 'cosh');
		$this->assertEqualsWithDelta(new Complex(1.166_736_257, -0.243_458_201_2), Complex::tanh($z), $accuracy, 'tanh');
		$this->assertEqualsWithDelta(new Complex(1.469_351_744, 1.063_440_024), Complex::arsinh($z), $accuracy, 'arsinh');
		$this->assertEqualsWithDelta(new Complex(1.528_570_919, 1.143_717_740), Complex::arcosh($z), $accuracy, 'arcosh');
		$this->assertEqualsWithDelta(new Complex(0.173_286_795_1, 1.178_097_245), Complex::artanh($z), $accuracy, 'artanh');
	}

	public function testCanComputeNonAnalytic() {
		$z = new Complex(1, 2);
		$accuracy = 1e-9;

		$this->assertEqualsWithDelta(sqrt(5), $z->abs(), $accuracy, 'abs');
		$this->assertEqualsWithDelta(1, $z->r(), $accuracy, 'r');
		$this->assertEqualsWithDelta(2, $z->i(), $accuracy, 'i');
		$this->assertEqualsWithDelta(1.107_148_718, $z->arg(), $accuracy, 'arg');
	}
}
