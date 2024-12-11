<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$config = new Nadystyle\Config();

$config->getFinder()
	->in(__DIR__ . '/tests')
	->in(__DIR__ . '/src');

$config->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');

return $config;
