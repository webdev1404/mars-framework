<?php

$this->post('get-csrf', function ($app) {
    return ['csrf' => $app->session->csrf];
}, 'get-csrf', '*');
