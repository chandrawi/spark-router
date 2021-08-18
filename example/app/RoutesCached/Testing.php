<?php
return SparkLib\SparkRouter\RouteFactory::__set_state(array(
   'staticRoutes' => 
  array (
    '/testing/' => 
    array (
      'GET' => 'Testing@index',
    ),
    '/testing/path/' => 
    array (
      'GET' => 'Testing@path',
    ),
    '/testing/path/sub-path/' => 
    array (
      'GET' => 'Testing@subPath',
    ),
  ),
   'dynamicRoutes' => 
  array (
    0 => 
    array (
      0 => '/testing/closure/',
      1 => 
      array (
        0 => 'input',
      ),
      2 => false,
      3 => 'GET',
      4 => NULL,
    ),
    1 => 
    array (
      0 => '/testing/',
      1 => 
      array (
        0 => 'id',
      ),
      2 => '~^/(\\d+)$~',
      3 => 'GET',
      4 => 'Testing@id',
    ),
    2 => 
    array (
      0 => '/testing/',
      1 => 
      array (
        0 => 'name',
      ),
      2 => false,
      3 => 'GET',
      4 => 'Testing@name',
    ),
  ),
));