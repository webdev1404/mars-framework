<?php
/**
* The Screens Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;

/**
 * The Screen Class
 * Contains 'Screen' functionality. Eg: error, message, fatal error screens etc..
 */
class Screens
{
    use InstanceTrait;

    /**
     * @var array $screens_list The list of supported screens
     */
    protected array $screens_list = [
        'error' => \Mars\Screens\Error::class,
        'message' => \Mars\Screens\Message::class,
        'fatal_error' => \Mars\Screens\FatalError::class,
        'permission_denied' => \Mars\Screens\PermissionDenied::class,
    ];    

    /**
     * @var Handlers $screens The screens handlers
     */
    public protected(set) Handlers $screens {
        get {
            if (isset($this->screens)) {
                return $this->screens;
            }   

            $this->screens = new Handlers($this->screens_list, null, $this->app);

            return $this->screens;
        }
    }

    /**
     * Displays an error screen
     * @param string $text The error's text
     * @param string $title The error's title, if any
     * @param bool $escape_html If true will escape the title and error message
     */
    public function error(string $text, string $title = '', ?bool $escape_html = null)
    {
        $this->screens->get('error')->output($text, $title, $escape_html);
    }

    /**
     * Displayes a message screen
     * @param string $text The text of the message
     * @param string $title The title of the message, if any
     * @param bool $escape_html If true will escape the title and message
     */
    public function message(string $text, string $title = '', ?bool $escape_html = null)
    {
        $this->screens->get('message')->output($text, $title, $escape_html);
    }

    /**
     * Displays an error screen
     * @param string $text The error's text
     * @param bool $escape_html If true will escape the error message
     */
    public function fatalError(string $text, bool $escape_html = null)
    {
        $this->screens->get('fatal_error')->output($text, $escape_html);
    }

    /**
     * Displays the Permission Denied screen
     */
    public function permissionDenied()
    {
        $this->screens->get('permission_denied')->output();
    }
}
