<?php declare(strict_types=1);

use MathParser\Exceptions\UnknownTokenException;
use MathParser\Lexing\{Lexer, Token, TokenDefinition, TokenType};
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase {
	private $lexer;

	public function setUp(): void {
		$lexer = new Lexer();

		$lexer->add(new TokenDefinition('/\d+/', TokenType::PosInt));
		$lexer->add(new TokenDefinition('/\+/', TokenType::AdditionOperator));

		$this->lexer = $lexer;
	}

	public function testCanTokenizeNumber() {
		$tokens = $this->lexer->tokenize('325');

		$this->assertTokenEquals('325', TokenType::PosInt, $tokens[0]);
	}

	public function testCanTokenizeOperator() {
		$tokens = $this->lexer->tokenize('+');

		$t = $tokens[0];
		$this->assertTokenEquals('+', TokenType::AdditionOperator, $t);
	}

	public function testCanTokenizeNumbersAndOperators() {
		$tokens = $this->lexer->tokenize('3+5');

		$this->assertCount(3, $tokens);

		$this->assertTokenEquals('3', TokenType::PosInt, $tokens[0]);
		$this->assertTokenEquals('+', TokenType::AdditionOperator, $tokens[1]);
		$this->assertTokenEquals('5', TokenType::PosInt, $tokens[2]);
	}

	public function testExceptionIsThrownOnUnknownToken() {
		$this->expectException(UnknownTokenException::class);

		$this->lexer->tokenize('@');
	}

	private function assertTokenEquals($value, $type, Token $token) {
		$this->assertEquals($value, $token->getValue());
		$this->assertEquals($type, $token->getType());
	}
}
