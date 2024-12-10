<?php

declare(strict_types=1);

namespace MathParser\Parsing\Nodes;

/**
 * AST node representing a number
 */
abstract class NumericNode extends Node
{
    /**
     * Returns the value
     */
    abstract public function getValue(): int|float;
}
