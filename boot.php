<?php
namespace Mars;

define('MARS', true);

try {
    $app = App::instantiate();
    $app->boot();
} catch (\Exception $e) {
    $app->fatalError($e->getMessage());
}
