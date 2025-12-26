<?php
/**
* The Escape Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Escapes Class
 * Escape values
 */
class Escape
{
    use Kernel;

    /**
     * Converts special chars. to html entities
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public function html(?string $value) : string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Escapes text meant to be written as javascript code, when embedding it with html. Eg: <a href="" onclick="<code>">
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public function js(string $value) : string
    {
        return $this->html($value);
    }

    /**
     * Escapes text which will be used inside javascript <script> tags
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public function jsString(string $value) : string
    {
        $json = json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        if ($json === false) {
            return '';
        }

        return substr($json, 1, -1);
    }

    /**
     * Escapes a folder path by hiding the server path
     * @param string $path The folder
     * @return string
     */
    public function path(string $path) : string
    {
        return str_replace($this->app->base_path, '', $path);
    }
}
