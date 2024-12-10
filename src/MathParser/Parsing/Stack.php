<?php

declare(strict_types=1);
/*
 * @package     Parsing
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Parsing;

/**
 * Utility class, implementing a simple FIFO stack
 * @template TValue
 */
class Stack
{
    /**
     * internal storage of data on the stack.
     * @var list<TValue>
     */
    protected array $data = [];

    /**
     * Push an element onto the stack.
     * @param TValue $element
     */
    public function push(mixed $element): void
    {
        $this->data[] = $element;
    }

    /**
     * Return the top element (without popping it)
     * @return TValue
     */
    public function peek(): mixed
    {
        return end($this->data);
    }

    /**
     * Return the top element and remove it from the stack.
     * @return TValue
     */
    public function pop(): mixed
    {
        return array_pop($this->data);
    }

    /**
     * Return the current number of elements in the stack.
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Returns true if the stack is empty
     *
     * @return boolean
     **/
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function __toString(): string
    {
        return implode(' ; ', $this->data);
    }
}
