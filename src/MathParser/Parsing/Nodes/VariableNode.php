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

use MathParser\Interpreting\Visitors\Visitor;

/**
 * AST node representing a variable
 */
class VariableNode extends Node {
	/** Name of represented variable, e.g. 'x' */
	private string $name;

	/** Constructor. Create a VariableNode with a given variable name. */
	public function __construct(string $name) {
		$this->name = $name;
	}

	/** Return the name of the variable */
	public function getName(): string {
		return $this->name;
	}

	/** Implementing the Visitable interface. */
	public function accept(Visitor $visitor): mixed {
		return $visitor->visitVariableNode($this);
	}

	/** Implementing the compareTo abstract method. */
	public function compareTo(?Node $other): bool {
		if ($other === null) {
			return false;
		}
		if (!($other instanceof VariableNode)) {
			return false;
		}

		return $this->getName() === $other->getName();
	}
}
