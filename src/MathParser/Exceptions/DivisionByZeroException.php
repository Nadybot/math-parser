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
 * Exception thrown when evaluating expressions containing a division by zero.
 */
class DivisionByZeroException extends MathParserException {
	/** Create a DivisionByZeroException */
	public function __construct() {
		parent::__construct('Division by zero.');
	}
}
