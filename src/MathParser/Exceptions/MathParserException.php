<?php

declare(strict_types=1);
/*
 * @package     Exceptions
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

/**
 * @namespace MathParser::Exceptions
 *
 * Exceptions thrown by the MathParser library.
 */

namespace MathParser\Exceptions;

/**
 * Base class for the exceptions thrown by the MathParser library.
 */
abstract class MathParserException extends \Exception {
	/** Additional information about the exception. */
	protected string $data;

	/** Get additional information about the exception. */
	public function getData(): string {
		return $this->data;
	}
}
