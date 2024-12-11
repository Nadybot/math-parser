<?php

declare(strict_types=1);

namespace MathParser\Parsing;

enum Associativity: int {
	case Left = 1;
	case Right = 2;
}
