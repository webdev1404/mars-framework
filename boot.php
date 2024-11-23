<?php
namespace Mars;

define('MARS', true);

try {
    $app = App::instantiate();    
    
    $app->plugins->run('boot');
} catch (\Exception $e) {
    $app->fatalError($e->getMessage());
}
