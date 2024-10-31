<?php
namespace Mars\Autoload;

use Mars\App;

/**
 * Autoloader for the extension files
 */
\spl_autoload_register(function ($name) {
    $allowed_extensions = ['Modules' => 'modules', 'Themes' => 'themes', 'Languages' => 'languages'];

    $parts = explode('\\', $name);
    $root = $parts[0];

    if (!isset($allowed_extensions[$root])) {
        return;
    }

    $app = App::get();

    $filename = $app->extensions_path . '/' . App::EXTENSIONS_DIRS[$allowed_extensions[$root]] . '/' . get_filename($parts);

    require($filename);
});

function get_filename(array $parts, int $base_parts = 1) : string
{
    $parts_count = count($parts);

    $path = '';
    $name = $parts[$parts_count - 1];

    //determine the dir and name of the class
    if ($parts_count > $base_parts + 1) {
        $path_parts = array_slice($parts, $base_parts, $parts_count - ($base_parts + 1));

        foreach ($path_parts as $i => $part) {
            $path_parts[$i] = convert_part($part);
        }

        $path = implode('/', $path_parts) . '/';
    }

    return $path . $name . '.php';
}

/**
 * Converts the path. Converts a namespace part like MyNamespace to folder my-namespace
 * @param array $parts The namespace parts
 * @return string The dir
 */
function convert_part(string $part) : string
{
    $new_part = '';
    $len = strlen($part);

    for ($i = 0; $i < $len; $i++) {
        $char = $part[$i];
        $ord = ord($char);

        if ($i && $ord >= 65 && $ord <= 90) {
            if ($i) {
                $new_part.= '-';
            }
        }

        $new_part.= $char;
    }

    return strtolower($new_part);
}