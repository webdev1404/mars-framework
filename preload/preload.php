<?php

//load the traits & interfaces
$files = require(__DIR__ . '/generated/traits-interfaces.php');
foreach ($files as $file) {
    opcache_compile_file($file);
}

//load the classes
$files = require(__DIR__ . '/generated/classes.php');
foreach ($files as $file) {
    opcache_compile_file($file);
}
