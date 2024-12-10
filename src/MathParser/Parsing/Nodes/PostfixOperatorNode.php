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
 * AST node representing a postfix operator. Only for temporary
 * use in the parser. The node will be converted to a FunctionNode
 * when consumed by the parser.
 */
class PostfixOperatorNode extends Node
{
    /** Name of the postfix operator. Currently, only '!' is possible. */
    private string $name;

    /** Constructor. Create a PostfixOperatorNode with given value. */
    public function __construct(string $name)
    {
        $this->name = $name;
    }


    /** returns the name of the postfix operator */
    public function getOperator(): string
    {
        return $this->name;
    }

    /**
     * Implementing the Visitable interface.
     */
    public function accept(Visitor $visitor): mixed
    {
        return null;
    }

    /** Implementing the compareTo abstract method. */
    public function compareTo(?Node $other): bool
    {
        if ($other === null) {
            return false;
        }
        if (!($other instanceof PostfixOperatorNode)) {
            return false;
        }

        return $this->getOperator() == $other->getOperator();
    }
}
