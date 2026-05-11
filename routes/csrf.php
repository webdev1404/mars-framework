<?php

$this->post('get-csrf', function () use ($app) {
    return ['csrf' => $app->session->csrf];
}, name: 'get-csrf');
