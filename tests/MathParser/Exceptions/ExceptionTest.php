<?php declare(strict_types=1);

namespace Tests\MathParser\Exceptions;

use MathParser\Exceptions\{UnknownConstantException, UnknownFunctionException, UnknownOperatorException, UnknownTokenException, UnknownVariableException};
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase {
	public function testUnknownTokenException() {
		try {
			throw new UnknownTokenException('@');
		} catch (UnknownTokenException $e) {
			$this->assertEquals($e->getData(), '@');
			$this->assertEquals($e->getName(), '@');
		}
	}

	public function testUnknownConstantException() {
		try {
			throw new UnknownConstantException('@');
		} catch (UnknownConstantException $e) {
			$this->assertEquals($e->getData(), '@');
			$this->assertEquals($e->getConstant(), '@');
		}
	}

	public function testUnknownFunctionException() {
		try {
			throw new UnknownFunctionException('@');
		} catch (UnknownFunctionException $e) {
			$this->assertEquals($e->getData(), '@');
			$this->assertEquals($e->getFunction(), '@');
		}
	}

	public function testUnknownOperatorException() {
		try {
			throw new UnknownOperatorException('@');
		} catch (UnknownOperatorException $e) {
			$this->assertEquals($e->getData(), '@');
			$this->assertEquals($e->getOperator(), '@');
		}
	}

	public function testUnknownVariableException() {
		try {
			throw new UnknownVariableException('@');
		} catch (UnknownVariableException $e) {
			$this->assertEquals($e->getData(), '@');
			$this->assertEquals($e->getVariable(), '@');
		}
	}
}
