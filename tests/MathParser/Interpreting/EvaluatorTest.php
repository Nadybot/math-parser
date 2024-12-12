<?php declare(strict_types=1);

namespace Tests\MathParser\Interpreting;

use MathParser\Exceptions\{DivisionByZeroException, UnknownConstantException, UnknownFunctionException, UnknownOperatorException, UnknownVariableException};
use MathParser\Interpreting\Evaluator;
use MathParser\Parsing\Nodes\{ConstantNode, ExpressionNode, FunctionNode, NumberNode, VariableNode};
use MathParser\{RationalMathParser, StdMathParser};
use PHPUnit\Framework\TestCase;

class EvaluatorTest extends TestCase {
	private StdMathParser $parser;
	private RationalMathParser $rparser;
	private Evaluator $evaluator;

	/** @var array<string,string> */
	private array $variables = [];

	public function setUp(): void {
		$this->parser = new StdMathParser();
		$this->rparser = new RationalMathParser();

		$this->variables = ['x' => '0.7', 'y' => '2.1', 'i' => '5'];
		$this->evaluator = new Evaluator($this->variables);
	}

	public function testCanEvaluateNumber() {
		$this->assertResult('3', 3);
		$this->assertResult('-2', -2);
		$this->assertResult('3.0', 3.0);

		$node = $this->rparser->parse('1/2');
		$this->assertEquals($this->evaluate($node), 0.5);
	}

	public function testCanEvaluateConstant() {
		$this->assertResult('pi', \M_PI);
		$this->assertResult('e', exp(1));

		$f = new ConstantNode('sdf');
		$this->expectException(UnknownConstantException::class);
		$value = $this->evaluate($f);
	}

	public function testCanEvaluateVariable() {
		$this->assertResult('x', $this->variables['x']);
		$this->assertResult('i^2', pow($this->variables['i'], 2));

		$this->expectException(UnknownVariableException::class);

		$f = $this->parser->parse('q');
		$this->evaluate($f);
	}

	public function testCanEvaluateAdditiion() {
		$x = $this->variables['x'];
		$this->assertResult('3+x', 3 + $x);
		$this->assertResult('3+x+1', 3 + $x + 1);
	}

	public function testCanEvaluateSubtraction() {
		$x = $this->variables['x'];
		$this->assertResult('3-x', 3 - $x);
		$this->assertResult('3-x-1', 3 - $x - 1);
	}

	public function testCanEvaluateUnaryMinus() {
		$this->assertResult('-x', -$this->variables['x']);
	}

	public function testCanEvaluateSignedOperators() {
		$x = $this->variables['x'];
		$this->assertResult('x*-5', $x * -5);
		$this->assertResult('-x*-5', -$x * -5);
		$this->assertResult('x--5', $x + 5);
		$this->assertResult('x-+5', $x - 5);
		$this->assertResult('x^-1', 1/$x);
		$this->assertResult('x/-2', $x/-2);
		$this->assertResult('x/+2', $x/2);
		$this->assertResult('x++2', $x+2);
		$this->assertResult('x+-2', $x-2);
	}

	public function testCanEvaluateMultiplication() {
		$x = $this->variables['x'];
		$this->assertResult('3*x', 3 * $x);
		$this->assertResult('3*x*2', 3 * $x * 2);
	}

	public function testCanEvaluateDivision() {
		$x = $this->variables['x'];
		$this->assertResult('3/x', 3 / $x);
		$this->assertResult('20/x/5', 20 / $x / 5);
	}

	public function testCannotDivideByZero() {
		$f = new ExpressionNode(3, '/', 0);

		$this->expectException(DivisionByZeroException::class);
		$value = $this->evaluate($f);
	}

	public function testCanEvaluateExponentiation() {
		$x = $this->variables['x'];
		$this->assertResult('x^3', pow($x, 3));
		$this->assertResult('x^x^x', pow($x, pow($x, $x)));
		$this->assertResult('(-1)^(-1)', -1);
	}

	public function testCantRaise0To0() {
		$this->expectException(DivisionByZeroException::class);
		$this->assertResult('0^0', 1);
	}

	public function testExponentiationExceptions() {
		$f = $this->parser->parse('0^(-1)');
		$value = $this->evaluate($f);

		$this->assertTrue(is_infinite($value));

		$f = $this->parser->parse('(-1)^(1/2)');
		$value = $this->evaluate($f);

		$this->assertTrue(is_nan($value));
	}

	public function testCanEvaluateSine() {
		$this->assertResult('sin(pi)', 0);
		$this->assertResult('sin(pi/2)', 1);
		$this->assertResult('sin(pi/6)', 0.5);
		$this->assertResult('sin(x)', sin((float)$this->variables['x']));
	}

	public function testCanEvaluateCosine() {
		$this->assertResult('cos(pi)', -1);
		$this->assertResult('cos(pi/2)', 0);
		$this->assertResult('cos(pi/3)', 0.5);
		$this->assertResult('cos(x)', cos((float)$this->variables['x']));
	}

	public function testCanEvaluateTangent() {
		$this->assertResult('tan(pi)', 0);
		$this->assertResult('tan(pi/4)', 1);
		$this->assertResult('tan(x)', tan((float)$this->variables['x']));
	}

	public function testCanEvaluateCotangent() {
		$this->assertResult('cot(pi/2)', 0);
		$this->assertResult('cot(pi/4)', 1);
		$this->assertResult('cot(x)', 1 / tan((float)$this->variables['x']));
	}

	public function testCanEvaluateArcsin() {
		$this->assertResult('arcsin(1)', \M_PI / 2);
		$this->assertResult('arcsin(1/2)', \M_PI / 6);
		$this->assertResult('arcsin(x)', asin((float)$this->variables['x']));

		$f = $this->parser->parse('arcsin(2)');
		$value = $this->evaluate($f);

		$this->assertIsNAN($value);
	}

	public function testCanEvaluateArccos() {
		$this->assertResult('arccos(0)', \M_PI / 2);
		$this->assertResult('arccos(1/2)', \M_PI / 3);
		$this->assertResult('arccos(x)', acos((float)$this->variables['x']));

		$f = $this->parser->parse('arccos(2)');
		$value = $this->evaluate($f);

		$this->assertIsNAN($value);
	}

	public function testCanEvaluateArctan() {
		$this->assertResult('arctan(1)', \M_PI / 4);
		$this->assertResult('arctan(x)', atan((float)$this->variables['x']));
	}

	public function testCanEvaluateArccot() {
		$this->assertResult('arccot(1)', \M_PI / 4);
		$this->assertResult('arccot(x)', \M_PI / 2 - atan((float)$this->variables['x']));
	}

	public function testCanEvaluateExp() {
		$this->assertResult('exp(x)', exp((float)$this->variables['x']));
	}

	public function testCanEvaluateLog() {
		$this->assertResult('log(x)', log((float)$this->variables['x']));

		$f = $this->parser->parse('log(-1)');
		$value = $this->evaluate($f);

		$this->assertIsNAN($value);
	}

	public function testCanEvaluateLn() {
		$this->assertResult('ln(x)', log((float)$this->variables['x']));

		$f = $this->parser->parse('ln(-1)');
		$value = $this->evaluate($f);

		$this->assertIsNAN($value);
	}

	public function testCanEvaluateLog10() {
		$this->assertResult('log10(x)', log((float)$this->variables['x']) / log(10));
	}

	public function testCanEvaluateFactorial() {
		$this->assertResult('0!', 1);
		$this->assertResult('3!', 6);
		$this->assertResult('(3!)!', 720);
		$this->assertResult('5!/(2!3!)', 10);
		$this->assertResult('5!!', 15);
		$this->assertApproximateResult('4.12124!', 28.854_554_91);
	}

	public function testCanEvaluateSqrt() {
		$this->assertResult('sqrt(x)', sqrt((float)$this->variables['x']));

		$f = $this->parser->parse('sqrt(-2)');
		$value = $this->evaluate($f);

		$this->assertIsNAN($value);
	}

	public function testCanEvaluateHyperbolicFunctions() {
		$x = (float)$this->variables['x'];

		$this->assertResult('sinh(0)', 0);
		$this->assertResult('sinh(x)', sinh($x));

		$this->assertResult('cosh(0)', 1);
		$this->assertResult('cosh(x)', cosh($x));

		$this->assertResult('tanh(0)', 0);
		$this->assertResult('tanh(x)', tanh($x));

		$this->assertResult('coth(x)', 1 / tanh($x));

		$this->assertResult('arsinh(0)', 0);
		$this->assertResult('arsinh(x)', asinh($x));

		$this->assertResult('arcosh(1)', 0);
		$this->assertResult('arcosh(3)', acosh(3));

		$this->assertResult('artanh(0)', 0);
		$this->assertResult('artanh(x)', atanh($x));

		$this->assertResult('arcoth(3)', atanh(1 / 3));
	}

	public function testCannotEvalauateUnknownFunction() {
		$f = new FunctionNode('sdf', new NumberNode(1));

		$this->expectException(UnknownFunctionException::class);
		$value = $this->evaluate($f);
	}

	public function testCannotEvaluateUnknownOperator() {
		$node = new ExpressionNode(new NumberNode(1), '+', new VariableNode('x'));
		// We need to cheat here, since the ExpressionNode contructor already
		// throws an UnknownOperatorException when called with, say '%'
		$node->setOperator('%');
		$this->expectException(UnknownOperatorException::class);

		$this->evaluate($node);
	}

	public function testCanCreateTemporaryUnaryMinusNode() {
		$node = new ExpressionNode(null, '~', null);
		$this->assertEquals($node->getOperator(), '~');
		$this->assertNull($node->getRight());
		$this->assertNull($node->getLeft());
		$this->assertEquals($node->getPrecedence(), 25);
	}

	public function testUnknownException() {
		$this->expectException(UnknownOperatorException::class);
		$node = new ExpressionNode(null, '%', null);
	}

	public function testEdgeCases() {
		$x = $this->variables['x'];

		$this->assertResult('0*log(0)', 0);

		$this->parser->setSimplifying(false);

		$this->assertIsNAN('0*log(0)');
		$this->assertResult('0^0', 1);
	}

	public function testCanComputeExponentialsTwoWays() {
		$this->assertEquals($this->compute('exp(1)'), $this->compute('e'));
		$this->assertEquals($this->compute('exp(2)'), $this->compute('e^2'));
		$this->assertEquals($this->compute('exp(-1)'), $this->compute('e^(-1)'));
		$this->assertEquals($this->compute('exp(8)'), $this->compute('e^8'));
		$this->assertEquals($this->compute('exp(22)'), $this->compute('e^22'));
	}

	public function testCanComputeSpecialValues() {
		$this->assertIsNAN('cot(0)');
		$this->assertIsNAN('cotd(0)');
		$this->assertIsNAN('coth(0)');
	}

	public function testCanComputeRoundingFunctions() {
		$this->assertResult('ceil(1+2.3)', 4);
		$this->assertResult('floor(2*2.3)', 4);
		$this->assertResult('ceil(2*2.3)', 5);
		$this->assertResult('round(2*2.3)', 5);
	}

	private function evaluate($f) {
		$this->evaluator->setVariables($this->variables);

		return $f->accept($this->evaluator);
	}

	private function compute($f) {
		return $this->evaluate($this->parser->parse($f));
	}

	private function assertResult($f, $x) {
		$accuracy = 1e-9;
		$value = $this->evaluate($this->parser->parse($f));
		$this->assertEqualsWithDelta($value, $x, $accuracy);
	}

	private function assertApproximateResult($f, $x) {
		$value = $this->evaluate($this->parser->parse($f));
		$this->assertEqualsWithDelta($value, $x, 1e-7);
	}

	private function assertIsNAN(string|float $f) {
		$value = $this->evaluate($this->parser->parse((string)$f));
		$this->assertNAN($value);
	}
}
