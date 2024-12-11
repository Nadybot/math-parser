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
 * Exception thrown when tokenizing expressions containing illegal
 * characters.
 */
class UnknownTokenException extends MathParserException {
	/** Create a UnknownTokenException */
	public function __construct(string $name) {
		parent::__construct("Unknown token {$name} encountered");

		$this->data = $name;
	}

	/** Get the unknown token that was encountered. */
	public function getName(): string {
		return $this->data;
	}
}
