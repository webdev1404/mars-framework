<?php
namespace Mars\Autoload;

use Mars\App;

/**
 * Autoloader for the extension files
 */
\spl_autoload_register(function ($name) {
    static $app;
    if (!isset($app)) {
        $app = App::obj();
    }

    static $handlers = [
        'Themes' => function () use ($app) {
            return $app->theme->manager;
        },
        'Languages' => function () use ($app) {
            return $app->lang->manager;
        },
        'Modules' => function () use ($app) {
            return $app->modules;
        },
    ];

    $parts = explode('\\', $name);
    if (count($parts) < 2) {
        return;
    }

    $root = $parts[0];
    if (!isset($handlers[$root])) {
        return;
    }

    $filename = $handlers[$root]()->getFilenameFromNamespace($parts);

    require($filename);
});
