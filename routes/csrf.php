<?php

$this->post('get-csrf', function ($app) {
    return ['csrf' => $app->session->csrf];
}, name: 'get-csrf');