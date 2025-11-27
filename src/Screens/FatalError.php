<?php
/**
* The Fatal Error Screen
* @package Mars
*/

namespace Mars\Screens;

use Mars\App\Kernel;

/**
 * The Fatal Error Screen
 * Displays a fatal error screen
 */
class FatalError
{
    use Kernel;

    /**
     * Displays a fatal error screen
     * @param string $text The error's text
     */
    public function output(string $text)
    {
        if ($this->app->is_cli) {
            echo 'Fatal Error: ' . $text . "\n";
            die;
        }


        $text = nl2br($this->app->escape->html($text));
        die;
    }
}
