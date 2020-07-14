<?php

require __DIR__."/../vendor/autoload.php";

use Symfony\Component\Console\Application;
use Console\Todo\Todos;

$app = new Application();

$app->add(new Todos());
$app->run();