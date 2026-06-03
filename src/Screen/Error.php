<?php
/**
* The Error Screen
* @package Mars
*/

namespace Mars\Screen;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Error Screen
 * Displays an error screen
 */
class Error
{
    use Kernel;

    /**
     * Displays the error screen
     * @param string $text The error's text
     * @param ?string $title The error's title, if any
     */
    public function render(string $text, ?string $title = null)
    {
        if ($this->app->is_cli) {
            $this->app->cli->error($text);
            return;
        }

        $this->app->theme->render('screen/error', ['title' => $title ?? App::__('message:error'), 'text' => $text]);
    }
}
