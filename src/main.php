<?php

use yuchanns\toybox\app\App;

require __DIR__ . '/../vendor/autoload.php';

define('APP_ROOT', __FILE__);

try {
    (new App)->run();
} catch (\Exception $e) {
    echo $e->getMessage();
    exit(-1);
}