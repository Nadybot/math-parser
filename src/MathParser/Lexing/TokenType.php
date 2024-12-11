<?php

declare(strict_types=1);
/*
 * @package     Lexical analysis
 * @subpackage  Token handling
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

namespace MathParser\Lexing;

/**
 * Token type values
 *
 * Currently, the following token types are available
 *
 * * PosInt
 * * Integer
 * * RealNumber
 * * Identifier
 * * OpenParenthesis
 * * CloseParenthesis
 * * UnaryMinus
 * * AdditionOperator
 * * SubtractionOperator
 * * MultiplicationOperator
 * * DivisionOperator
 * * ExponentiationOperator
 * * FunctionName
 * * Constant
 * * Terminator
 * * Whitespace
 * * Sentinel
 */
enum TokenType: int {
	/** Token representing a positive integer */
	case PosInt = 1;

	/** Token representing a (not necessarily positive) integer */
	case Integer = 2;

	/** Token representing a floating point number */
	case RealNumber = 3;

	/** Token representing an identifier, i.e. a variable name. */
	case Identifier = 20;

	/** Token representing an opening parenthesis, i.e. '(' */
	case OpenParenthesis = 31;

	/** Token representing a closing parenthesis, i.e. ')' */
	case CloseParenthesis = 32;

	/** Token representing a unary minus. Not used. This is the responsibility of the Parser */
	case UnaryMinus = 99;

	/** Token representing '+' */
	case AdditionOperator = 100;

	/** Token representing '-' */
	case SubtractionOperator = 101;

	/** Token representing '*' */
	case MultiplicationOperator = 102;

	/** Token representing '/' */
	case DivisionOperator = 103;

	/** Token representing '^' */
	case ExponentiationOperator = 104;

	/** Token representing postfix factorial operator '!' */
	case FactorialOperator = 105;

	/** Token representing postfix subfactorial operator '!!' */
	case SemiFactorialOperator = 106;

	/** Token represented a function name, e.g. 'sin' */
	case FunctionName = 200;

	/** Token represented a known constant, e.g. 'pi' */
	case Constant = 300;

	/** Token representing a terminator, e.g. ';'. Currently not used. */
	case Terminator = 998;

	/** Token representing white space, e.g. spaces and tabs. */
	case Whitespace = 999;

	/** Token representing a senitinel, for internal used in the Parser. Not used. */
	case Sentinel = 1_000;
}
