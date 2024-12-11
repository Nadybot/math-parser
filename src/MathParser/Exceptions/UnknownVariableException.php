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
 * unknown or undefined variable.
 */
class UnknownVariableException extends MathParserException {
	/** Constructor. Create a UnknownVariableException */
	public function __construct(string $variable) {
		parent::__construct("Unknown variable {$variable}.");

		$this->data = $variable;
	}

	/** Get the unkown variable. */
	public function getVariable(): string {
		return $this->data;
	}
}
