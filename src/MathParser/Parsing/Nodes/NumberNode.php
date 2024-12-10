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
 * AST node representing a number (int or float)
 */
class NumberNode extends NumericNode
{
    /** The value of the represented number. */
    private int|float $value;

    /** Constructor. Create a NumberNode with given value. */
    public function __construct(int|float $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value
     */
    public function getValue(): int|float
    {
        return $this->value;
    }

    /**
     * Implementing the Visitable interface.
     */
    public function accept(Visitor $visitor): mixed
    {
        return $visitor->visitNumberNode($this);
    }

    /** Implementing the compareTo abstract method. */
    public function compareTo(?Node $other): bool
    {
        if ($other === null) {
            return false;
        }
        if (!($other instanceof NumberNode)) {
            return false;
        }

        return $this->getValue() == $other->getValue();
    }
}
