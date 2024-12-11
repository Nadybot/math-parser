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
 * unknown constant.
 *
 * This should not happen under normal circumstances.
 */
class UnknownConstantException extends MathParserException {
	/** Constructor. Create a UnknownConstantException. */
	public function __construct(string $operator) {
		parent::__construct("Unknown constant {$operator}.");

		$this->data = $operator;
	}

	/** Get the unkown constant that was encountered. */
	public function getConstant(): string {
		return $this->data;
	}
}
