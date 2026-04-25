<?php
/**
* The Email Class
* @package Mars
*/

namespace Mars\Mvc\Controller;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\HiddenProperty;
use Mars\Mvc\Controller;


class Email
{
    use Kernel;

    /**
     * @var Controller $controller The controller the email object belongs to
     */
    #[HiddenProperty]
    protected Controller $controller;

    /**
     * @var array $data_array The array of data extracted from the email template
     */
    protected array $data_array = [];

    /**
     * Builds the email object
     * @param Controller $controller The controller the email object belongs to
     * @param App $app The app object
     */
    public function __construct(Controller $controller, App $app)
    {
        $this->controller = $controller;
        $this->app = $app;
    }

    /**
     * Gets the email body from a template
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     * @param string|null $dir The directory where the template is located. If not set, 'emails' will be used
     * @return string The email body
     */
    public function get(string $template, array $vars = [], string $dir = 'emails') : string
    {
        $body = nl2br($this->controller->view->getTemplateByLanguage($dir, $template, $vars));

        $this->data_array = $this->app->theme->template->data;

        return $body;
    }

    /**
     * @internal
     */
    public function __isset($name)
    {
        return isset($this->data_array[$name]);
    }

    /**
     * @internal
     */
    public function __get($name)
    {
        return $this->data_array[$name] ?? null;
    }

    /**
     * @internal
     */
    public function __set($name, $value)
    {
        $this->data_array[$name] = $value;
    }
}