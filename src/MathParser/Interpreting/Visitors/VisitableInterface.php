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

/**
 * Interfaces required to implement the visitor design pattern.
 *
 * Two interfaces are required:
 * - *Visitable* should be implemented by classes to be visited, i.e. subclasses of Node.
 *      This interface consists of a single function `accept()`, called to visit the AST.
 * - *Visitor* should be implemented by AST transformers, and consists of one function
 *      for each subclass of Node, i.e. `visitXXXNode()`
 */

namespace MathParser\Interpreting\Visitors;

/**
 * Visitable interface,
 *
 * Part of the visitor design pattern implementation. Every Node
 * implements the Visitable interface, containing the single function
 * accept()
 *
 * Implemented by the (abstract) Node class.
 *
 * ### Example
 *
 * ```php
 * $node = new ExpressionNode(1, '+', 2);
 * $visitor = new TreePrinter();    // Or any other Visitor
 * $node->accept();
 * ```
 */
interface VisitableInterface {
	/**
	 * Single function in the Visitable interface
	 *
	 * Calling the accept() function on a Visitable class,
	 * i.e. a Node (or subclass thereof) causes the supplied
	 * Visitor to traverse the AST.
	 */
	public function accept(VisitorInterface $visitor): mixed;
}
