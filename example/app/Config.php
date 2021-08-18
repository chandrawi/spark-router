<?php

define('BASE_URI', '/');
define('BASE_DIR', dirname(__DIR__, 1));

define('ROUTE_DIR', BASE_DIR.'/app/Routes');
define('ROUTE_FILES', array(
    ['/', '/Index.php'],
    ['/testing', '/Testing.php'],
));
