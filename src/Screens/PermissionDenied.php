<?php
/**
* The Permission Denied Screen
* @package Mars
*/

namespace Mars\Screens;

use Mars\App\InstanceTrait;

/**
 * The Permission Denied Screen
 * Displays the Permission Denied screen
 */
class PermissionDenied
{
    use InstanceTrait;

    /**
     * Displays the Permission Denied screen
     */
    public function output()
    {
        echo 'Permission denied!' . "\n";
        die;
    }
}
