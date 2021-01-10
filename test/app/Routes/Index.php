<?php

use SparkLib\SparkRouter\RouteFactory;

$route = new RouteFactory;

$route->get('/', 'Index@index');

$route->get('/{data}', 'Index@data');

return $route;
