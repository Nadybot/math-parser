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
 * AST node representing a known constant (e.g. pi, e)
 */
class ConstantNode extends Node
{
    /**
     * Name of the constant, e.g. 'pi' or 'e'.
     *
     * string $value
     **/
    private string $value;

    /**
     * Constructor.
     *
     * ### Example
     *
     * ~~~{.php}
     * $node = new ConstantNode('pi');
     * ~~~
     *
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
    * @property getName
    *
    * Returns the name of the constant
    */
    public function getName(): string
    {
        return $this->value;
    }

    /**
     * Implementing the Visitable interface.
     */
    public function accept(Visitor $visitor): mixed
    {
        return $visitor->visitConstantNode($this);
    }

    /** Implementing the compareTo abstract method. */
    public function compareTo(?Node $other): bool
    {
        if ($other === null) {
            return false;
        }
        if (!($other instanceof ConstantNode)) {
            return false;
        }

        return $this->getName() == $other->getName();
    }
}
