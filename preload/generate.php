<?php
namespace Mars\Preload;

use Mars\App;

require(dirname(__DIR__, 4) . '/vendor/autoload.php');
require(__DIR__ . '/functions.php');

$app = App::obj();
$base_dir = $app->base_path . '/vendor/webdev1404/mars-framework/src';

$files = $app->dir->getFiles($base_dir, true);
$traits_and_interfaces = get_traits_and_interfaces($files);
write_file(__DIR__ . '/generated/traits-interfaces.php', $traits_and_interfaces);

$classes = get_classes($files);
$classes = sort_classes($classes);
write_file(__DIR__ . '/generated/classes.php', $classes);

$app->cli->print('Preload list generated');
