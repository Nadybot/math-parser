<?php

declare(strict_types=1);
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*/

namespace MathParser\Interpreting;

use MathParser\Interpreting\Visitors\Visitor;
use MathParser\Parsing\Nodes\{ConstantNode, ExpressionNode, FunctionNode, IntegerNode, NumberNode, RationalNode, VariableNode};

/**
 * Simple string representation of an AST. Probably most
 * useful for debugging purposes.
 *
 * Implementation of a Visitor, transforming an AST into a string
 * representation of the tree.
 *
 * ## Example:
 *
 * ```php
 * $parser = new StdMathParser();
 * $f = $parser->parse('exp(2x)+xy');
 * printer = new TreePrinter();
 * result = $f->accept($printer);    // Generates "(+ (exp (* 2 x)) (* x y))"
 * ```
 */
class TreePrinter implements Visitor {
	/** Print an ExpressionNode. */
	public function visitExpressionNode(ExpressionNode $node): string {
		$leftValue = $node->getLeft()->accept($this);
		$operator = $node->getOperator();

		// The operator and the right side are optional, remember?
		if (!$operator) {
			return "{$leftValue}";
		}

		$right = $node->getRight();

		if ($right) {
			$rightValue = $node->getRight()->accept($this);
			return "({$operator}, {$leftValue}, {$rightValue})";
		}
			return "({$operator}, {$leftValue})";

	}

	/** Print a NumberNode. */
	public function visitNumberNode(NumberNode $node): string {
		$val = $node->getValue();
		return "{$val}:float";
	}

	public function visitIntegerNode(IntegerNode $node): string {
		$val = $node->getValue();
		return "{$val}:int";
	}

	public function visitRationalNode(RationalNode $node): string {
		$p = $node->getNumerator();
		$q = $node->getDenominator();
		return "{$p}/{$q}:rational";
	}

	/** Print a VariableNode. */
	public function visitVariableNode(VariableNode $node): string {
		return $node->getName();
	}

	/** Print a FunctionNode. */
	public function visitFunctionNode(FunctionNode $node): string {
		$functionName = $node->getName();
		$operand = $node->getOperand()->accept($this);

		return "{$functionName}({$operand})";
	}

	/** Print a ConstantNode. */
	public function visitConstantNode(ConstantNode $node): string {
		return $node->getName();
	}
}
