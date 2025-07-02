<?php
/**
* The Permission Denied Screen
* @package Mars
*/

namespace Mars\Screens;

use Mars\App\Kernel;

/**
 * The Permission Denied Screen
 * Displays the Permission Denied screen
 */
class PermissionDenied
{
    use Kernel;

    /**
     * Displays the Permission Denied screen
     */
    public function output()
    {
        echo 'Permission denied!' . "\n";
        die;
    }
}
