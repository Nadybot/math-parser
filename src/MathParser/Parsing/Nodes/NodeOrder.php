<?php

declare(strict_types=1);

/**
 * Node classes for use in the generated abstract syntax trees (AST).
 */

namespace MathParser\Parsing\Nodes;

enum NodeOrder: int {
	case None = 0;
	case Integer = 1;
	case Rational = 2;
	case Float = 3;
}
