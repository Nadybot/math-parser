<?php

declare(strict_types=1);
/*
 * Visitable interface
 *
 * Part of the visitor design pattern implementation. Every Node
 * implements the Visitable interface, containing the single function
 * accept()
 *
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Interpreting\Visitors;

use MathParser\Parsing\Nodes\{ConstantNode, ExpressionNode, FunctionNode, IntegerNode, NumberNode, RationalNode, VariableNode};

/**
 * Visitor interface
 *
 * Implemented by every interpreter. The interface specifies
 * functions for visiting and handling each Node subclass.
 */
interface Visitor {
	/**
	 * Interface function for visiting ExpressionNodes
	 *
	 * @param ExpressionNode $node Node to visit.
	 */
	public function visitExpressionNode(ExpressionNode $node): mixed;

	/**
	 * Interface function for visiting NumberNodes
	 *
	 * @param NumberNode $node Node to visit.
	 */
	public function visitNumberNode(NumberNode $node): mixed;

	/**
	 * Interface function for visiting IntegerNodes
	 *
	 * @param IntegerNode $node Node to visit.
	 */
	public function visitIntegerNode(IntegerNode $node): mixed;

	/**
	 * Interface function for visiting RationalNodes
	 *
	 * @param RationalNode $node Node to visit.
	 */
	public function visitRationalNode(RationalNode $node): mixed;

	/**
	 * Interface function for visiting VariableNodes
	 *
	 * @param VariableNode $node Node to visit.
	 */
	public function visitVariableNode(VariableNode $node): mixed;

	/**
	 * Interface function for visiting FunctionNode
	 *
	 * @param FunctionNode $node Node to visit.
	 */
	public function visitFunctionNode(FunctionNode $node): mixed;

	/**
	 * Interface function for visiting ConstantNode
	 *
	 * @param ConstantNode $node Node to visit.
	 */
	public function visitConstantNode(ConstantNode $node): mixed;
}
