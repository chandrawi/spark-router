<?php
return SparkLib\SparkRouter\RouteFactory::__set_state(array(
   'staticRoutes' => 
  array (
    '/' => 
    array (
      'GET' => 'Index@index',
    ),
  ),
   'dynamicRoutes' => 
  array (
    0 => 
    array (
      0 => '/',
      1 => 
      array (
        0 => 'data',
      ),
      2 => false,
      3 => 'GET',
      4 => 'Index@data',
    ),
  ),
));