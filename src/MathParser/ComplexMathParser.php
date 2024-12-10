<?php

declare(strict_types=1);

namespace MathParser;

/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/



use MathParser\Parsing\Parser;
use MathParser\Lexing\ComplexLexer;
use MathParser\Parsing\Nodes\Node;

class ComplexMathParser extends AbstractMathParser
{
    public function __construct()
    {
        $this->lexer = new ComplexLexer();
        $this->parser = new Parser();
        $this->parser->setRationalFactory(true);
    }

    /**
     * Parse the given mathematical expression into an abstract syntax tree.
     *
     * @param string $text Input
     */
    public function parse(string $text): Node
    {
        $this->tokens = $this->lexer->tokenize($text);
        $this->tree = $this->parser->parse($this->tokens);

        return $this->tree;
    }
}
