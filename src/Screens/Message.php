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
     * Displays the message screen
     * @param string $text The message's text
     * @param string|null $title The message's title, if any
     */
    public function render(string $text, ?string $title = null)
    {
        if ($this->app->is_cli) {
            $this->app->cli->message($text);
            return;
        }

        $this->app->theme->render('screen/message', ['title' => $title ?? App::__('message:message'), 'text' => $text]);
    }
}
