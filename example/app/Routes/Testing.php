<?php

use SparkLib\SparkRouter\RouteFactory;

$route = new RouteFactory;

$route->groupPrefix("/testing");

$route->get('/', 'Testing@index');

$route->get('/closure/{input}', function ($input) {
    echo "<h1>Closure Route</h1><br/>";
    echo "<h2>input : $input</h2>";
});

$route->get('/path', 'Testing@path');
$route->get('/path/sub-path', 'Testing@subPath');
$route->get('/{id:\d+}', 'Testing@id');
$route->get('/{name}', 'Testing@name');

return $route;
