<?php

$router->post('get-csrf', function() use($app) {
    return ['csrf' => $app->session->csrf];
});