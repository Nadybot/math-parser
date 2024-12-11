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

use MathParser\Exceptions\UnknownOperatorException;
use MathParser\Interpreting\Visitors\Visitor;
use MathParser\Parsing\Associativity;
use MathParser\Parsing\Nodes\Traits\Sanitize;

/**
 * AST node representing a binary operator
 */
class ExpressionNode extends Node {
	use Sanitize;

	/** Node $left Left operand */
	private ?Node $left=null;

	/** string $operator Operator, e.g. '+', '-', '*', '/' or '^' */
	private ?string $operator=null;

	/** Node $right Right operand */
	private ?Node $right=null;

	/** int $precedence Precedence. Operators with higher prcedence bind harder */
	private int $precedence;

	/** Associativity of operator. */
	private Associativity $associativity;

	/**
	 * Constructor
	 *
	 * Construct a binary operator node from (one or) two operands and an operator.
	 *
	 * For convenience, the constructor accept int or float as operands, automatically
	 * converting these to NumberNodes
	 *
	 * ### Example
	 *
	 * ```php
	 * $node = new ExpressionNode(1,'+',2);
	 * ```
	 *
	 * @param Node|null|int|float $left     First operand
	 * @param ?string             $operator Name of operator
	 * @param Node|null|int|float $right    Second operand
	 */
	public function __construct(
		null|Node|int|float $left,
		?string $operator=null,
		null|Node|int|float $right=null
	) {
		$this->left = $this->sanitize($left);
		$this->operator = $operator;
		$this->right = $this->sanitize($right);

		switch ($operator) {
			case '+':
				$this->precedence = 10;
				$this->associativity = Associativity::Left;
				break;

			case '-':
				$this->precedence = 10;
				$this->associativity = Associativity::Left;
				break;

			case '*':
				$this->precedence = 20;
				$this->associativity = Associativity::Left;
				break;

			case '/':
				$this->precedence = 20;
				$this->associativity = Associativity::Left;
				break;

			case '~':
				$this->precedence = 25;
				$this->associativity = Associativity::Left;
				break;

			case '^':
				$this->precedence = 30;
				$this->associativity = Associativity::Right;
				break;

			default:
				throw new UnknownOperatorException((string)$operator);
		}
	}

	/**
	 * Return the first (left) operand.
	 *
	 * @return Node|null
	 */
	public function getLeft() {
		return $this->left;
	}

	/** Set the left operand. */
	public function setLeft(Node $operand): void {
		$this->left = $operand;
	}

	/** Return the operator. */
	public function getOperator(): string {
		return $this->operator;
	}

	/** Set the operator. */
	public function setOperator(string $operator): void {
		$this->operator = $operator;
	}

	/** Return the second (right) operand. */
	public function getRight(): ?Node {
		return $this->right;
	}

	/** Set the right operand. */
	public function setRight(Node $operand): void {
		$this->right = $operand;
	}

	/**
	 * Return the precedence of the ExpressionNode.
	 *
	 * @return int precedence
	 */
	public function getPrecedence() {
		return $this->precedence;
	}

	/** Implementing the Visitable interface. */
	public function accept(Visitor $visitor): mixed {
		return $visitor->visitExpressionNode($this);
	}

	/**
	 * Returns true if the node can represent a unary operator, i.e. if
	 * the operator is '+' or '-'-
	 *
	 * @return bool
	 */
	public function canBeUnary() {
		return in_array($this->operator, ['+', '-', '~'], true);
	}

	/**
	 * Returns true if the current Node has lower precedence than the one
	 * we compare with.
	 *
	 * In case of a tie, we also consider the associativity.
	 * (Left associative operators are lower precedence in this context.)
	 *
	 * @param false|Node $other Node to compare to.
	 */
	public function lowerPrecedenceThan(false|Node $other): bool {
		if (!($other instanceof ExpressionNode)) {
			return false;
		}

		if ($this->getPrecedence() < $other->getPrecedence()) {
			return true;
		}
		if ($this->getPrecedence() > $other->getPrecedence()) {
			return false;
		}

		return $this->associativity === Associativity::Left;
	}

	public function strictlyLowerPrecedenceThan(false|Node $other): bool {
		if (!($other instanceof ExpressionNode)) {
			return false;
		}

		return $this->getPrecedence() < $other->getPrecedence();
	}

	/** Implementing the compareTo abstract method. */
	public function compareTo(?Node $other): bool {
		if ($other === null) {
			return false;
		}
		if (!($other instanceof ExpressionNode)) {
			return false;
		}

		if ($this->getOperator() !== $other->getOperator()) {
			return false;
		}

		$thisLeft = $this->getLeft();
		$otherLeft = $other->getLeft();
		$thisRight = $this->getRight();
		$otherRight = $other->getRight();

		if ($thisLeft === null) {
			return $otherLeft === null && $thisRight->compareTo($otherRight);
		}

		if ($thisRight === null) {
			return $otherRight=== null && $thisLeft->compareTo($otherLeft);
		}

		return $thisLeft->compareTo($otherLeft) && $thisRight->compareTo($otherRight);
	}
}
