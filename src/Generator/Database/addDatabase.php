<?php

use Codingbeard\Devtools\Generator\Database\DatabaseGenerator;

require __DIR__ . '/../../../vendor/autoload.php';

if (!isset($argv[1]))
{
  die("Please provide a database name" . PHP_EOL);
}

if (!isset($argv[2]))
{
  $argv[2] = 'accounts';
}

$name = $argv[1];
$copy = $argv[2];

$generator = new DatabaseGenerator(__DIR__ . '/../../../config/', $name, $copy);

$generator->generate();

echo "Don't forget to add it to \\WZ\\Site\\WZAPI\\Common\\Library\\Services and then update the DI" . PHP_EOL;
