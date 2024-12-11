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
 * unknown oprator.
 *
 * This should not happen under normal circumstances.
 */
class UnknownOperatorException extends MathParserException {
	/** Create a UnknownOperatorException */
	public function __construct(string $operator) {
		parent::__construct("Unknown operator {$operator}.");

		$this->data = $operator;
	}

	/** Get the unkown operator that was encountered. */
	public function getOperator(): string {
		return $this->data;
	}
}
