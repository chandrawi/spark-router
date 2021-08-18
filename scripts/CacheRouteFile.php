<?php

require __DIR__ .'/../example/app/Config.php';
require __DIR__ .'/../vendor/autoload.php';

use SparkLib\SparkRouter\RouteFactory;

if ($argc >= 3) {
    $routeFile = $argv[1];
    $cacheFile = $argv[2];
    $cacheDir = substr($cacheFile, 0, strrpos($cacheFile, '/'));
} else {
    exit('Invalid route file or cache file argument');
}

if (file_exists($routeFile) && file_exists($cacheDir)) {
    $route = require $routeFile;
    if ($route instanceof RouteFactory) {
        $route->removeNonExportProperties();
        $route->removeClosureAction();
        $export = "<?php\r\nreturn ". var_export($route, true) .";";
        file_put_contents($cacheFile, $export);
    } else {
        exit('Invalid route file content');
    }
} else {
    exit('Route file or cache directory does not exists');
}
