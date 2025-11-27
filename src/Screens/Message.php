<?php
/**
* The Message Screen
* @package Mars
*/

namespace Mars\Screens;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Message Screen
 * Displays a message screen
 */
class Message
{
    use Kernel;

    /**
     * Outputs the message screen
     * @param string $text The message's text
     * @param string $title The message's title, if any
     */
    public function output(string $text, ?string $title = null)
    {
        if ($this->app->is_cli) {
            $this->app->cli->message($text);
            return;
        }

        $this->app->theme->render('message/message', ['title' => $title ?? App::__('message.message'), 'text' => $text]);
    }
}
