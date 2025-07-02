<?php
/**
* The Error Screen
* @package Mars
*/

namespace Mars\Screens;

use Mars\App\Kernel;

/**
 * The Error Screen
 * Displays an error screen
 */
class Error
{
    use Kernel;

    /**
     * Outputs the error screen
     * @param string $text The error's text
     * @param string $title The error's title, if any
     * @param bool $escape_html If true will escape the title and error message
     */
    public function output(string $text, string $title = '', ?bool $escape_html = null)
    {
        $escape_html = $escape_html ?? $this->app->is_web;

        if ($escape_html) {
            $text = $this->app->escape->html($text);
        }

        echo 'Error: ' . $text . "\n";
        die;
    }
}
