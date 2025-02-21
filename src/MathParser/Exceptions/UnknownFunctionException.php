<?php

declare(strict_types=1);
/*
 * @package     Exceptions
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Exceptions;

/**
 * Exception thrown when parsing or evaluating expressions containing an
 * unknown function symbol.
 *
 * This should not happen under normal circumstances.
 */
class UnknownFunctionException extends MathParserException {
	/** Create a UnknownFunctionException */
	public function __construct(string $operator) {
		parent::__construct("Unknown function {$operator}.");

		$this->data = $operator;
	}

	/** Get the unkown function that was encountered. */
	public function getFunction(): string {
		return $this->data;
	}
}
