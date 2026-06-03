<?php
/**
* The Permission Denied Screen
* @package Mars
*/

namespace Mars\Screen;

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
    public function render()
    {
        if ($this->app->is_cli) {
            $this->app->cli->error('Permission denied!');
            return;
        }

        $this->app->theme->render('screen/permission-denied');
    }
}
