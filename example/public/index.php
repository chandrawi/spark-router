<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../app/Config.php';

require '../../vendor/autoload.php';

use SparkLib\App\Loader;

$app = new Loader;

// $app->run();

$app->runCached();

// $app->test();
