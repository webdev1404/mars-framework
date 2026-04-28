<?php

use Mars\App;

$app = App::obj();
if (!$app->is_cli) {
    die("The setup script must be run as a CLI application\n");
}

$app->cli->printLn();
$app->cli->print("*********************************", 'important');
$app->cli->print("Welcome to the Mars setup script!", 'important');
$app->cli->print("*********************************", 'important');
$app->cli->printLn();

$base_url = $app->cli->ask('Please enter the base URL: ');
$site_name = $app->cli->ask('Please enter the site name: ');
$site_emails = $app->cli->ask('Please enter the site email(s) (comma separated): ');



/**
//create symlinks for cache folders
$symlinks = [    
    'vendor/webdev1404/mars-framework/assets' => 'public/assets/framework',
    'app/assets' => 'public/assets/app',
    'data/cache/css' => 'public/assets/cache/css',
    'data/cache/js' => 'public/assets/cache/js'
];
foreach ($symlinks as $target => $link) {
    $target = $base_dir . '/' . $target;
    $link = $base_dir . '/' . $link;

    if (!is_dir($link)) {
        symlink($target, $link);
    }
} 

 */