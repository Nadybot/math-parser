<?php

declare(strict_types=1);
/*
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2016 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

namespace MathParser\Interpreting;

use MathParser\Exceptions\{UnknownConstantException, UnknownOperatorException};
use MathParser\Interpreting\Visitors\VisitorInterface;
use MathParser\Parsing\Nodes\{ConstantNode, ExpressionNode, FunctionNode, IntegerNode, Node, NumberNode, RationalNode, VariableNode};

/**
 * Create LaTeX code for prettyprinting a mathematical expression
 * (for example via MathJax)
 *
 * Implementation of a Visitor, transforming an AST into a string
 * giving LaTeX code for the expression.
 *
 * The class in general does *not* generate the best possible LaTeX
 * code, and needs more work to be used in a production setting.
 *
 * ## Example:
 * ```php
 * $parser = new StdMathParser();
 * $f = $parser->parse('exp(2x)+xy');
 * printer = new LaTeXPrinter();
 * result = $f->accept($printer);    // Generates "e^{2x}+xy"
 * ```
 *
 * Note that surrounding `$`, `$$` or `\begin{equation}..\end{equation}`
 * has to be added manually.
 */
class ASCIIPrinter implements VisitorInterface {
	/**
	 * Generate ASCII output code for an ExpressionNode
	 *
	 * Create a string giving ASCII output representing an ExpressionNode `(x op y)`
	 * where `op` is one of `+`, `-`, `*`, `/` or `^`
	 *
	 * @param ExpressionNode $node AST to be typeset
	 */
	public function visitExpressionNode(ExpressionNode $node): string {
		$operator = $node->getOperator();
		$left = $node->getLeft();
		$right = $node->getRight();

		switch ($operator) {
			case '+':
				$leftValue = $left->accept($this);
				$rightValue = $this->parenthesize($right, $node);
				return "{$leftValue}+{$rightValue}";

			case '-':
				if ($right) {
					// Binary minus

					$leftValue = $left->accept($this);
					$rightValue = $this->parenthesize($right, $node);
					return "{$leftValue}-{$rightValue}";
				}
				// Unary minus
				$leftValue = $this->parenthesize($left, $node);
				return "-{$leftValue}";

			case '*':
			case '/':
				$leftValue = $this->parenthesize($left, $node, '', false);
				$rightValue = $this->parenthesize($right, $node, '', true);
				return "{$leftValue}{$operator}{$rightValue}";

			case '^':
				$leftValue = $this->parenthesize($left, $node, '', true);
				$rightValue = $this->parenthesize($right, $node, '', false);
				return "{$leftValue}{$operator}{$rightValue}";

			default:
				throw new UnknownOperatorException($operator);
		}
	}

	public function visitNumberNode(NumberNode $node): string {
		$val = $node->getValue();

		return "{$val}";
	}

	public function visitIntegerNode(IntegerNode $node): string {
		$val = $node->getValue();

		return "{$val}";
	}

	public function visitRationalNode(RationalNode $node): string {
		$p = $node->getNumerator();
		$q = $node->getDenominator();
		if ($q === 1.0) {
			return "{$p}";
		}

		return "{$p}/{$q}";
	}

	public function visitVariableNode(VariableNode $node): string {
		return $node->getName();
	}

	public function visitFunctionNode(FunctionNode $node): string {
		$functionName = $node->getName();

		if ($functionName === '!' || $functionName === '!!') {
			return $this->visitFactorialNode($node);
		}

		$operand = $node->getOperand()->accept($this);

		return "{$functionName}({$operand})";
	}

	public function visitConstantNode(ConstantNode $node): string {
		switch ($node->getName()) {
			case 'pi':
				return 'pi';
			case 'e':
				return 'e';
			case 'i':
				return 'i';
			case 'NAN':
				return 'NAN';
			case 'INF':
				return 'INF';

			default:
				throw new UnknownConstantException($node->getName());
		}
	}

	public function parenthesize(Node $node, ExpressionNode $cutoff, string $prepend='', bool $conservative=false): string {
		$text = $node->accept($this);

		if ($node instanceof ExpressionNode) {
			// Second term is a unary minus
			if ($node->getOperator() === '-' && $node->getRight() === null) {
				return "({$text})";
			}

			if ($cutoff->getOperator() === '-' && $node->lowerPrecedenceThan($cutoff)) {
				return "({$text})";
			}

			if ($conservative) {
				// Add parentheses more liberally for / and ^ operators,
				// so that e.g. x/(y*z) is printed correctly
				if ($cutoff->getOperator() === '/' && $node->lowerPrecedenceThan($cutoff)) {
					return "({$text})";
				}
				if ($cutoff->getOperator() === '^' && $node->getOperator() === '^') {
					return "({$text})";
				}
			}

			if ($node->strictlyLowerPrecedenceThan($cutoff)) {
				return "({$text})";
			}
		}

		if (($node instanceof NumberNode || $node instanceof IntegerNode || $node instanceof RationalNode) && $node->getValue() < 0) {
			return "({$text})";
		}

		// Treat rational numbers as divisions on printing
		if ($node instanceof RationalNode && $node->getDenominator() !== 1.0) {
			$fakeNode = new ExpressionNode($node->getNumerator(), '/', $node->getDenominator());

			if ($fakeNode->lowerPrecedenceThan($cutoff)) {
				return "({$text})";
			}
		}

		return "{$prepend}{$text}";
	}

	private function visitFactorialNode(FunctionNode $node): string {
		$functionName = $node->getName();
		$op = $node->getOperand();
		$operand = $op->accept($this);

		// Add parentheses most of the time.
		if ($op instanceof NumberNode || $op instanceof IntegerNode || $op instanceof RationalNode) {
			if ($op->getValue() < 0) {
				$operand = "({$operand})";
			}
		} elseif ($op instanceof VariableNode || $op instanceof ConstantNode) {
			// Do nothing
		} else {
			$operand = "({$operand})";
		}

		return "{$operand}{$functionName}";
	}
}
