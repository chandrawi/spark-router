<?php

require __DIR__ .'/../example/app/Config.php';
require __DIR__ .'/../vendor/autoload.php';

use SparkLib\SparkRouter\RouteFactory;

if ($argc >= 3) {
    $routeDir = $argv[1];
    $cacheDir = $argv[2];
} else {
    exit('Invalid route directory or cache directory argument');
}

if (file_exists($routeDir) && file_exists($cacheDir)) {
    $files = scandir($routeDir);
    foreach ($files as $file) {
        if (substr($file, -3) == 'php') {
            echo $file ."\n";

            $route = require $filePath = $routeDir .'/'. $file;
            if ($route instanceof RouteFactory) {
                $route->removeNonExportProperties();
                $route->transformClosureAction($filePath);
                $export = "<?php\r\nreturn ". var_export($route, true) .";";
                file_put_contents($cacheDir .'/'. $file, $export);
            } else {
                exit('Invalid route file content: '. $file);
            }
        }
    }
} else {
    exit('Route file or cache directory does not exists');
}
