<?php
/**
* The View Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\App\Kernel;
use Mars\HiddenProperty;
use Mars\Config;
use Mars\Document;
use Mars\Escape;
use Mars\Filter;
use Mars\Format;
use Mars\Hidden;
use Mars\Html;
use Mars\Text;
use Mars\Ui;
use Mars\System\Plugins;
use Mars\System\Uri;
use Mars\Extensions\Extension;

/**
 * The View Class
 * Implements the View functionality of the MVC pattern
 */
abstract class View
{
    use Kernel;

    /**
     * @var string $default_method Default method to be executed on dispatch/route or if the requested method doesn't exist or is not public
     */
    public string $default_method {
        get {
            if (isset($this->default_method)) {
                return $this->default_method;
            }

            //set the default methods to the name of the extension, if not already set
            $this->default_method = '';
            if ($this->parent) {
                $this->default_method = $this->app->getMethod($this->parent->name);
            }
           
            return $this->default_method;
        }
    }

    /**
     * @var string $root The root folder used to load templates from
     */
    public protected(set) string $root {
        get {
            if (isset($this->root)) {
                return $this->root;
            }

            $this->root = strtolower($this->controller->name) . '/';

            return $this->root;
        }
    }

    /**
     * @var string $path The controller's parents's dir. Alias for $this->parent->path
     */
    public string $path {
        get => $this->controller->path;
    }

    /**
     * @var string $assets_path The folder where the assets files are stored
     */
    public string $assets_path {
        get => $this->controller->assets_path;
    }

    /**
     * @var string $assets_url The url pointing to the folder where the assets for the extension are located
     */
    public string $assets_url {
        get => $this->controller->assets_url;
    }

    /**
     * @var Extension $parent The parent extension
     */
    #[HiddenProperty]
    protected ?Extension $parent {
        get => $this->controller->parent ?? null;
    }

    /**
     * @var Controller $controller The controller the view belongs to
     */
    #[HiddenProperty]
    protected Controller $controller;

    /**
     * @var object $model The model
     */
    #[HiddenProperty]
    protected object $model {
        get => $this->controller->model;
    }

    /**
     * @var Document $document Alias for $this->app->document
     */
    #[HiddenProperty]
    protected Document $document {
        get => $this->app->document;
    }

    /**
     * @var Config $config The config object. Alias for $this->app->config
     */
    #[HiddenProperty]
    protected Config $config {
        get => $this->app->config;
    }

    /**
     * @var Html $html Alias for $this->app->html
     */
    #[HiddenProperty]
    protected Html $html {
        get => $this->app->html;
    }

    /**
     * @var Escape $escape Alias for $this->app->escape
     */
    #[HiddenProperty]
    protected Escape $escape {
        get => $this->app->escape;
    }

    /**
     * @var Filter $filter Alias for $this->app->filter
     */
    #[HiddenProperty]
    protected Filter $filter {
        get => $this->app->filter;
    }

    /**
     * @var Format $format Alias for $this->app->format
     */
    #[HiddenProperty]
    protected Format $format {
        get => $this->app->format;
    }

    /**
     * @var Uri $url Alias for $this->app->url
     */
    #[HiddenProperty]
    public Uri $url {
        get => $this->app->url;
    }

    /**
     * @var Ui $ui Alias for $this->app->ui
     */
    #[HiddenProperty]
    protected Ui $ui {
        get => $this->app->ui;
    }

    /**
     * @var Text $uri Alias for $this->app->text
     */
    #[HiddenProperty]
    protected Text $text {
        get => $this->app->text;
    }

    /**
     * @var Plugins $plugins Alias for $this->app->plugins
     */
    #[HiddenProperty]
    protected Plugins $plugins {
        get => $this->app->plugins;
    }

    /**
     * Builds the View
     * @param Controller $controller The controller the view belongs to
     * @param App $app the app object
     */
    public function __construct(App $app, ?Controller $controller = null)
    {
        $this->app = $app;
        $this->controller = $controller;

        $this->init();
    }

    /**
     * Inits the view. Method which can be overriden in custom views to init properties etc..
     */
    protected function init()
    {
    }

    /**
     * Alias for $this->app->lang->get()
     */
    protected function __(string $str, array $replace = [], string $prefix = '') : string
    {
        return $this->app->lang->get($str, $replace, $prefix);
    }

    /**
     * Renders a template.
     * @param string $method The method to render. If not set, the current method will be used
     * @param array $vars Vars to pass to the template, if any
     */
    public function render(?string $method = null, array $vars = [])
    {
        $template = $this->get(vars: $vars, method: $method);
        if ($template === null) {
            return;
        }

        echo $template;
    }

    /**
     * Returns the contents of a template.
     * @param string $template The name of the template to load. If not set, the method's name will be used
     * @param array $vars Vars to pass to the template, if any
     * @param string $method The method to execute before loading the template. If not set, the current method will be used
     * @return string The contents of the template
     */
    public function get(string $template = '', array $vars = [], ?string $method = null) : ?string
    {
        $method = $this->getMethodName($method);

        if ($this->canDispatch($method)) {
            $ret = $this->$method();

            if ($ret === false) {
                return null;
            } elseif (is_string($ret)) {
                return $ret;
            }
        }

        if (!$template) {
            $template = $this->getTemplateName($method);
        }

        return $this->getTemplate($template, $vars);
    }

    /**
     * Returns the method name to be executed on dispatch/route
     * @param string|null $method The requested method name
     * @return string The method name to be executed
     */
    protected function getMethodName(?string $method) : string
    {
        $method ??= $this->controller->current_method;
        if (!$method) {
            $method = $this->default_method;
        }

        if (str_contains($method, '::')) {
            //has __METHOD__ format
            $method = substr($method, strrpos($method, '::') + 2);
        }

        return $method;
    }

    /**
     * Returns the vars to be passed to the template
     * @param array $vars The initial vars
     * @return array The vars
     */
    protected function getVars(array $vars) : array
    {
        return array_merge(['view' => $this, 'model' => $this->model], $vars);
    }

    /**
     * Returns the name of a template to load
     * @param string $method The currently executed method
     * @return string The template's name
     */
    protected function getTemplateName(string $method) : string
    {
        $template = preg_replace('/([A-Z])/', '-$1', $method);
        $template = strtolower($template);

        return $template;
    }

    /**
     * Returns the contents of a template
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     * @return string|null The contents of the template or null if the template doesn't exist
     */
    public function getTemplate(string $template, array $vars = []) : ?string
    {
        return $this->parent->getTemplate($this->root . $template, $this->getVars($vars));
    }

    /**
     * Returns the contents of a language template
     * @param string $dir The directory where the template is located
     * @param string $template The name of the template to load. If not set, the method's name will be used
     * @param array $vars Vars to pass to the template, if any
     * @return string|null The contents of the template or null if the template doesn't exist
     */
    public function getLanguageTemplate(string $dir, string $template, array $vars = []) : ?string
    {
        return $this->parent->getLanguageTemplate($this->root . $dir, $template, $this->getVars($vars));
    }

    /**
     * Returns the contents of an email template
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     * @param string|null $dir The directory where the template is located. If not set, 'emails' will be used
     * @return string|null The contents of the template or null if the template doesn't exist
     */
    public function getEmailTemplate(string $template, array $vars = [], ?string $dir = null) : ?string
    {
        $dir = $dir ?? 'emails';

        return nl2br($this->getLanguageTemplate($dir, $template, $vars));
    }

    /**
     * Determines if a method call can be dispatched
     * @param string $method The name of the method
     * @return bool
     */
    protected function canDispatch(string $method) : bool
    {
        if (!method_exists($this, $method)) {
            return false;
        }

        $rm = new \ReflectionMethod($this, $method);

        if ($rm->isConstructor() || $rm->isDestructor()) {
            return false;
        }

        if (!$rm->isPublic()) {
            return false;
        }

        return true;
    }

    /**
     * Returns a data value.
     * @param string $name The name of the data
     * @return mixed The data value
     */
    public function getData(string $name)
    {
        return $this->app->theme->getData($name);
    }
}
