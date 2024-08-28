<?php
namespace Mars\Autoload;

/**
 * Autoloader for the app files
 */
\spl_autoload_register(function ($name) {
    if (!str_starts_with($name, 'App\\')) {
        return;
    }

    $parts = explode('\\', $name);

    $filename = dirname(__DIR__, 3) . '/app/' . get_filename($parts);

    require($filename);
});
