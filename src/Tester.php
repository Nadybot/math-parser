
<?php

use MathParser\Interpreting\{ASCIIPrinter, Evaluator};
use MathParser\StdMathParser;

include __DIR__ . '/../vendor/autoload.php';

$parser = new StdMathParser();
$parser->setSimplifying(false);

$tree = $parser->parse($argv[1]);
// echo "String conversion: {$tree}\n";

$ascii = new ASCIIPrinter();
echo 'ASCII: ' . $tree->accept($ascii) . "\n";

try {
	echo 'Evaluator: ';
	$evaluator = new Evaluator();
	$evaluator->setVariables(['x' => 17]);
	echo $tree->accept($evaluator) . "\n";
} catch (\Exception $e) {
	var_dump($e->getMessage());
}
