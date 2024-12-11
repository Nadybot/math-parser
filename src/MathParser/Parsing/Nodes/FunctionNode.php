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

use MathParser\Interpreting\Visitors\VisitorInterface;

/**
 * AST node representing a function applications (e.g. sin(...))
 */
class FunctionNode extends Node {
	/** Function name, e.g. 'sin' */
	private string $name;

	/** AST of function operand */
	private ?Node $operand=null;

	/** Constructor, create a FunctionNode with given name and operand */
	public function __construct(string $name, null|int|Node $operand) {
		$this->name = $name;
		if (is_int($operand)) {
			$operand = new NumberNode($operand);
		}
		$this->operand = $operand;
	}

	/** Return the name of the function */
	public function getName(): string {
		return $this->name;
	}

	/** Return the operand */
	public function getOperand(): ?Node {
		return $this->operand;
	}

	/** Set the operand */
	public function setOperand(Node $operand): Node {
		return $this->operand = $operand;
	}

	public function getOperator(): string {
		return $this->name;
	}

	/** Implementing the Visitable interface. */
	public function accept(VisitorInterface $visitor): mixed {
		return $visitor->visitFunctionNode($this);
	}

	/** Implementing the compareTo abstract method. */
	public function compareTo(?Node $other): bool {
		if ($other === null) {
			return false;
		}
		if (!($other instanceof FunctionNode)) {
			return false;
		}

		$thisOperand = $this->getOperand();
		$otherOperand = $other->getOperand();

		return $this->getName() === $other->getName() && $thisOperand->compareTo($otherOperand);
	}
}
