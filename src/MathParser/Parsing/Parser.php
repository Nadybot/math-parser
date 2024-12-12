<?php

declare(strict_types=1);
/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/


/**
 * @namespace MathParser::Parsing
 *
 * Parser related classes
 */

namespace MathParser\Parsing;

use MathParser\Exceptions\{ParenthesisMismatchException, SyntaxErrorException, UnknownOperatorException};
use MathParser\Lexing\{Token, TokenType};
use MathParser\Parsing\Nodes\Factories\NodeFactory;
use MathParser\Parsing\Nodes\{ExpressionNode, FunctionNode, IntegerNode, Node, NumberNode, PostfixOperatorNode, RationalNode, SubExpressionNode};

/**
 * Mathematical expression parser, based on the shunting yard algorithm.
 *
 * Parse a token string into an abstract syntax tree (AST).
 *
 * As the parser loops over the individual tokens, two stacks are kept
 * up to date. One stack ($operatorStack) consists of hitherto unhandled
 * tokens corresponding to ''operators'' (unary and binary operators, function
 * applications and parenthesis) and a stack of parsed sub-expressions (the
 * $operandStack).
 *
 * If the current token is a terminal token (number, variable or constant),
 * a corresponding node is pushed onto the operandStack.
 *
 * Otherwise, the precedence of the current token is compared to the top
 * element(t) on the operatorStack, and as long as the current token has
 * lower precedence, we keep popping operators from the stack to constuct
 * more complicated subexpressions together with the top items on the operandStack.
 *
 * Once the token list is empty, we pop the remaining operators as above, and
 * if the formula was well-formed, the only thing remaining on the operandStack
 * is a completely parsed AST, which we return.
 */
class Parser {
	/**
	 * List of tokens to process
	 *
	 * @var list<Token>
	 */
	protected array $tokens;

	/**
	 * Stack stack of operators waiting to process
	 *
	 * @var Stack<Node>
	 */
	protected Stack $operatorStack;

	/**
	 * Stack stack of operands waiting to process
	 *
	 * @var Stack<Node>
	 */
	protected Stack $operandStack;

	protected bool $rationalFactory = false;
	protected bool $simplifyingParser = true;

	/** Constructor */
	public function __construct(
		protected NodeFactory $nodeFactory=new NodeFactory()
	) {
	}

	public function setRationalFactory(bool $flag): void {
		$this->rationalFactory = $flag;
	}

	public function setSimplifying(bool $flag): void {
		$this->simplifyingParser = $flag;
	}

	/**
	 * Parse list of tokens
	 *
	 * @param list<Token> $tokens Array (Token[]) of input tokens.
	 *
	 * @return Node AST representing the parsed expression.
	 */
	public function parse(array $tokens): Node {
		// Filter away any whitespace
		$tokens = $this->filterTokens($tokens);
		$tokens = $this->parseSignedNumbers($tokens);

		// Insert missing implicit multiplication tokens
		if (static::allowImplicitMultiplication()) {
			$tokens = $this->parseImplicitMultiplication($tokens);
		}

		$this->tokens = $tokens;

		// Perform the actual parsing
		return $this->shuntingYard($tokens);
	}

	/**
	 * Populate node with operands.
	 *
	 * @throws SyntaxErrorException
	 */
	protected function handleExpression(Node $node): Node {
		if ($node instanceof FunctionNode) {
			throw new ParenthesisMismatchException($node->getOperator());
		}
		if ($node instanceof SubExpressionNode) {
			throw new ParenthesisMismatchException($node->getOperator());
		}

		if (($node instanceof ExpressionNode) && !$this->simplifyingParser) {
			return $this->naiveHandleExpression($node);
		}

		if ($node->getOperator() === '~') {
			$left = $this->operandStack->pop();
			if ($left === null) {
				throw new SyntaxErrorException();
			}

			if ($left instanceof NumberNode) {
				return new NumberNode(-$left->getValue());
			}

			if ($left instanceof IntegerNode) {
				return new IntegerNode(-$left->getValue());
			}

			if ($left instanceof RationalNode) {
				return new RationalNode(-$left->getNumerator(), $left->getDenominator());
			}

			if (!($node instanceof ExpressionNode)) {
				throw new SyntaxErrorException();
			}

			$node->setOperator('-');
			$node->setLeft($left);

			return $this->nodeFactory->simplify($node);
		}

		$right = $this->operandStack->pop();
		$left = $this->operandStack->pop();
		if ($right === null || $left === null) {
			throw new SyntaxErrorException();
		}

		if (!($node instanceof ExpressionNode)) {
			throw new SyntaxErrorException();
		}
		$node->setLeft($left);
		$node->setRight($right);

		return $this->nodeFactory->simplify($node);
	}

	/**
	 * Populate node with operands, without any simplification.
	 *
	 * @throws SyntaxErrorException
	 */
	protected function naiveHandleExpression(ExpressionNode $node): ExpressionNode {
		if ($node->getOperator() === '~') {
			$left = $this->operandStack->pop();
			if ($left === null) {
				throw new SyntaxErrorException();
			}

			$node->setOperator('-');
			$node->setLeft($left);

			return $node;
		}

		$right = $this->operandStack->pop();
		$left = $this->operandStack->pop();
		if ($right === null || $left === null) {
			throw new SyntaxErrorException();
		}

		$node->setLeft($left);
		$node->setRight($right);

		return $node;
	}

	/**
	 * Remove Whitespace from the token list.
	 *
	 * @param list<Token> $tokens Input list of tokens
	 *
	 * @return list<Token>
	 */
	protected function filterTokens(array $tokens): array {
		$filteredTokens = array_filter($tokens, static function (Token $t): bool {
			return $t->getType() !== TokenType::Whitespace;
		});

		// Return the array values only, because array_filter preserves the keys
		return array_values($filteredTokens);
	}

	/**
	 * Insert multiplication tokens where needed (taking care of implicit mulitplication).
	 *
	 * @param list<Token> $tokens Input list of tokens
	 *
	 * @return list<Token>
	 */
	protected function parseImplicitMultiplication(array $tokens): array {
		$result = [];
		$lastToken = null;
		foreach ($tokens as $token) {
			if (Token::canFactorsInImplicitMultiplication($lastToken, $token)) {
				$result[] = new Token('*', TokenType::MultiplicationOperator);
			}
			$lastToken = $token;
			$result[] = $token;
		}
		return $result;
	}

	/**
	 * Make `-`/`+` followed by a number a negative number if needed
	 *
	 * @param list<Token> $tokens Input list of tokens
	 *
	 * @return list<Token>
	 */
	protected function parseSignedNumbers(array $tokens): array {
		$result = [];
		for ($i = 0; $i < count($tokens); $i++) {
			$token = $tokens[$i];
			$type = $token->getType();
			$isNumber = in_array($type, [TokenType::PosInt, TokenType::Integer, TokenType::RealNumber], true);
			if ($i < 2 || !$isNumber) {
				$result[] = $token;
				continue;
			}
			$prePreType = $tokens[$i-2]->getType();
			$preType = $tokens[$i-1]->getType();
			$prePreIsOperator = in_array($prePreType, [
				TokenType::DivisionOperator,
				TokenType::MultiplicationOperator,
				TokenType::AdditionOperator,
				TokenType::SubtractionOperator,
				TokenType::ExponentiationOperator,
			], true);
			$preIsSign = in_array($preType, [
				TokenType::AdditionOperator,
				TokenType::SubtractionOperator,
			], true);
			if ($prePreIsOperator && $preIsSign) {
				array_pop($result);
				if ($preType === TokenType::SubtractionOperator) {
					switch ($type) {
						case TokenType::PosInt:
						case TokenType::Integer:
							$token = new Token((string)(-1 * (int)$token->getValue()), TokenType::Integer);
							break;
						case TokenType::RealNumber:
							$token = new Token((string)(-1 * (float)$token->getValue()), TokenType::RealNumber);
							break;
					}
				}
			}
			$result[] = $token;
		}
		return $result;
	}

	/**
	 * Determine if the parser allows implicit multiplication. Create a
	 * subclass of Parser, overriding this function, returning false instead
	 * to diallow implicit multiplication.
	 *
	 * ### Example:
	 *
	 * ```php
	 * class ParserWithoutImplicitMultiplication extends Parser {
	 *   protected static function allowImplicitMultiplication(): bool {
	 *     return false;
	 *   }
	 * }
	 *
	 * $lexer = new StdMathLexer();
	 * $tokens = $lexer->tokenize('2x');
	 * $parser = new ParserWithoutImplicitMultiplication();
	 * $node = $parser->parse($tokens); // Throws a SyntaxErrorException
	 * ```
	 */
	protected static function allowImplicitMultiplication(): bool {
		return true;
	}

	/**
	 * Determine if `$node` is in fact a unary operator.
	 *
	 * If `$node` can be a unary operator (i.e. is a '+' or '-' node),
	 * **and** this is the first node we parse or the previous node
	 * was a SubExpressionNode, i.e. an opening parenthesis, or the
	 * previous node was already a unary minus, this means that the
	 * current node is in fact a unary '+' or '-' and we return true,
	 * otherwise we return false.
	 *
	 * @param ExpressionNode $node     Current node
	 * @param ?Node          $lastNode Previous node handled by the Parser
	 */
	protected function isUnary(ExpressionNode $node, ?Node $lastNode): bool {
		if (!($node->canBeUnary())) {
			return false;
		}

		// Unary if it is the first token
		if ($this->operatorStack->isEmpty() && $this->operandStack->isEmpty()) {
			return true;
		}
		// or if the previous token was '('
		if ($lastNode instanceof SubExpressionNode) {
			return true;
		}
		// or last node was already a unary minus
		return $lastNode instanceof ExpressionNode && $lastNode->getOperator() === '~';
	}

	/**
	 * Handle a closing parenthesis, popping operators off the
	 * operator stack until we find a matching opening parenthesis.
	 *
	 * @throws ParenthesisMismatchException
	 */
	protected function handleSubExpression(): void {
		// Flag, checking for mismatching parentheses
		$clean = false;

		// Pop operators off the operatorStack until its empty, or
		// we find an opening parenthesis, building subexpressions
		// on the operandStack as we go.
		while ($popped = $this->operatorStack->pop()) {
			// ok, we have our matching opening parenthesis
			if ($popped instanceof SubExpressionNode) {
				$clean = true;
				break;
			}

			$node = $this->handleExpression($popped);
			$this->operandStack->push($node);
		}

		// Throw an error if the parenthesis couldn't be matched
		if (!$clean) {
			throw new ParenthesisMismatchException();
		}


		// Check to see if the parenthesis pair was in fact part
		// of a function application. If so, create the corresponding
		// FunctionNode and push it onto the operandStack.
		$previous = $this->operatorStack->peek();
		if ($previous instanceof FunctionNode) {
			$node = $this->operatorStack->pop();
			$operand = $this->operandStack->pop();
			assert($node instanceof FunctionNode);
			$node->setOperand($operand);
			$this->operandStack->push($node);
		}
	}

	/**
	 * Implementation of the shunting yard parsing algorithm
	 *
	 * @param list<Token> $tokens Array of tokens to process
	 *
	 * @return Node AST of the parsed expression
	 *
	 * @throws SyntaxErrorException
	 * @throws ParenthesisMismatchException
	 */
	private function shuntingYard(array $tokens): Node {
		// Clear the oepratorStack
		$this->operatorStack = new Stack();

		// Clear the operandStack.
		$this->operandStack = new Stack();

		// Remember the last token handled, this is done to recognize unary operators.
		$lastNode = null;

		// Loop over the tokens
		foreach ($tokens as $token) {
			if ($this->rationalFactory) {
				$node = Node::rationalFactory($token);
			} else {
				$node = Node::factory($token);
			}

			if ($token->getType() === TokenType::CloseParenthesis) {
				// Handle closing parentheses
				$this->handleSubExpression();
			} elseif ($node->isTerminal()) {
				// Push terminal tokens on the operandStack
				$this->operandStack->push($node);
			} elseif ($node instanceof FunctionNode) {
				// Push function applications or open parentheses `(` onto the operatorStack
				$this->operatorStack->push($node);
			} elseif ($node instanceof SubExpressionNode) {
				$this->operatorStack->push($node);
			} elseif ($node instanceof PostfixOperatorNode) {
				// Handle the remaining operators.
				$op = $this->operandStack->pop();
				if ($op === null) {
					throw new SyntaxErrorException();
				}
				$this->operandStack->push(new FunctionNode($node->getOperator(), $op));
			} elseif ($node instanceof ExpressionNode) {
				// Check for unary minus and unary plus.
				$unary = $this->isUnary($node, $lastNode);

				if ($unary) {
					switch ($token->getType()) {
						// Unary +, just ignore it
						case TokenType::AdditionOperator:
							$node = null;
							break;
							// Unary -, replace the token.
						case TokenType::SubtractionOperator:
							$node->setOperator('~');
							break;
						default:
							throw new UnknownOperatorException('Unknown unary operator: ' . $token->getType()->name);
					}
				} else {
					// Pop operators with higher priority

					while ($node->lowerPrecedenceThan($this->operatorStack->peek())) {
						$popped = $this->operatorStack->pop();
						assert(!is_null($popped));
						$popped = $this->handleExpression($popped);
						$this->operandStack->push($popped);
					}
				}

				if ($node) {
					$this->operatorStack->push($node);
				}
			}

			// Remember the current token (if it hasn't been nulled, for example being a unary +)
			if ($node) {
				$lastNode = $node;
			}
		}


		// Pop remaining operators

		while (!$this->operatorStack->isEmpty()) {
			$node = $this->operatorStack->pop();
			$node = $this->handleExpression($node);
			$this->operandStack->push($node);
		}

		// Stack should be empty here
		if ($this->operandStack->count() > 1) {
			throw new SyntaxErrorException();
		}

		return $this->operandStack->pop();
	}
}
