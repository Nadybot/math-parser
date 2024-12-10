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
 *
 */
final class TokenType
{
    /** Token representing a positive integer */
    public const PosInt = 1;
    /** Token representing a (not necessarily positive) integer */
    public const Integer = 2;
    /** Token representing a floating point number */
    public const RealNumber = 3;

    /** Token representing an identifier, i.e. a variable name. */
    public const Identifier = 20;
    /** Token representing an opening parenthesis, i.e. '(' */
    public const OpenParenthesis = 31;
    /** Token representing a closing parenthesis, i.e. ')' */
    public const CloseParenthesis = 32;

    /** Token representing a unary minus. Not used. This is the responsibility of the Parser */
    public const UnaryMinus = 99;
    /** Token representing '+' */
    public const AdditionOperator = 100;
    /** Token representing '-' */
    public const SubtractionOperator = 101;
    /** Token representing '*' */
    public const MultiplicationOperator = 102;
    /** Token representing '/' */
    public const DivisionOperator = 103;
    /** Token representing '^' */
    public const ExponentiationOperator = 104;
    /** Token representing postfix factorial operator '!' */
    public const FactorialOperator = 105;
    /** Token representing postfix subfactorial operator '!!' */
    public const SemiFactorialOperator = 105;

    /** Token represented a function name, e.g. 'sin' */
    public const FunctionName = 200;

    /** Token represented a known constant, e.g. 'pi' */
    public const Constant = 300;

    /** Token representing a terminator, e.g. ';'. Currently not used. */
    public const Terminator = 998;
    /** Token representing white space, e.g. spaces and tabs. */
    public const Whitespace = 999;

    /** Token representing a senitinel, for internal used in the Parser. Not used. */
    public const Sentinel = 1000;
}
