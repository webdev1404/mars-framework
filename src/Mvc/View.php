<?php
/**
* The View Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Document;
use Mars\Escape;
use Mars\Filter;
use Mars\Format;
use Mars\Html;
use Mars\Text;
use Mars\Ui;
use Mars\Uri;
use Mars\System\Plugins;
use Mars\Extensions\Extension;

/**
 * The View Class
 * Implements the View functionality of the MVC pattern
 */
abstract class View
{
    use InstanceTrait;

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
     * @var string $template The name of the template which will be rendered when render() is called
     */
    public protected(set) string $template = '';

    /**
     * @var string $path The controller's parents's dir. Alias for $this->parent->path
     */
    public protected(set) string $path {
        get {
            if (isset($this->path)) {
                return $this->path;
            }

            $this->path = '';
            if ($this->parent) {
                $this->path = $this->parent->path;
            }

            return $this->path;
        }
    }

    /**
     * @var string $url The controller's parent's url. Alias for $this->parent->url
     */
    public protected(set) string $url {
        get {
            if (isset($this->url)) {
                return $this->url;
            }

            $this->url = '';
            if ($this->parent) {
                $this->url = $this->parent->url;
            }

            return $this->url;
        }
    }

    /**
     * @var string $url_static The controller's parent's url static. Alias for $this->parent->url_static
     */
    public protected(set) string $url_static {
        get {
            if (isset($this->url_static)) {
                return $this->url_static;
            }

            $this->url_static = '';
            if ($this->parent) {
                $this->url_static = $this->parent->url_static;
            }

            return $this->url_static;
        }
    }

    /**
     * @var Extension $parent The parent extension
     */
    protected ?Extension $parent {
        get => $this->controller->parent;
    }

    /**
     * @var Controller $controller The controller
     */
    public protected(set) Controller $controller;

    /**
     * @var object $model The model
     */
    protected  object $model {
        get => $this->controller->model;
    }

    /**
     * @var Document $document Alias for $this->app->document
     */
    #[Hidden]
    protected Document $document {
        get => $this->app->document;
    }

    /**
     * @var Html $html Alias for $this->app->html
     */
    #[Hidden]
    protected Html $html {
        get => $this->app->html;
    }

    /**
     * @var Escape $escape Alias for $this->app->escape
     */
    #[Hidden]
    protected Escape $escape {
        get => $this->app->escape;
    }

    /**
     * @var Filter $filter Alias for $this->app->filter
     */
    #[Hidden]
    protected Filter $filter {
        get => $this->app->filter;
    }

    /**
     * @var Format $format Alias for $this->app->format
     */
    #[Hidden]
    protected Format $format {
        get => $this->app->format;
    }

    /**
     * @var Uri $uri Alias for $this->app->uri
     */
    #[Hidden]
    public Uri $uri {
        get => $this->app->uri;
    }

    /**
     * @var Ui $ui Alias for $this->app->ui
     */
    #[Hidden]
    protected Ui $ui {
        get => $this->app->ui;
    }

    /**
     * @var Text $uri Alias for $this->app->text
     */
    #[Hidden]
    protected Text $text {
        get => $this->app->text;
    }

    /**
     * @var Plugins $plugins Alias for $this->app->plugins
     */
    #[Hidden]
    protected Plugins $plugins {
        get => $this->app->plugins;
    }

    /**
     * Builds the View
     * @param Controller $controller The controller the view belongs to
     * @param App $app the app object
     */
    public function __construct(Controller $controller, ?App $app = null)
    {
        $this->app = $app ?? $this->getApp();
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
     * Sets the title of the current page
     * @param string $title The title
     * @return static
     */
    protected function setTitle(string $title) : static
    {
        $this->app->title->set($title);

        return $this;
    }

    /**
     * Renders a template.
     * @param array $vars Vars to pass to the template, if any
     */
    public function render(array $vars = [])
    {
        $method = $this->controller->current_method;
        if (!$method) {
            $method = $this->default_method;
        }

        if ($this->canDispatch($method)) {
            $ret = $this->$method();

            if ($ret === false) {
                return;
            } elseif (is_string($ret)) {
                echo $ret;
                return;
            }
        } else {
            return;
        }

        $this->renderTemplate($this->getTemplateName($method), $vars);
    }

    /**
     * Renders a template
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     */
    protected function renderTemplate(string $template, array $vars = [])
    {
        //add the view's public properties as theme vars
        $this->app->theme->addVars(get_object_vars($this));
        $this->app->theme->addVar('view', $this);

        $this->parent->render($template, $vars);
    }

    /**
     * Returns the contents of a template from the extension's module's dir
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     * @return string The contents of the template
     */
    /*protected function renderModuleTemplate(string $template, array $vars = [])
    {
        $this->app->theme->addVars(get_object_vars($this));
        $this->app->theme->addVar('view', $this);

        return $this->parent->getModuleTemplate($template, $vars);
    }*/

    /**
     * Returns the name of a template to load
     * @param string $method The currently executed method
     * @return string The template's name
     */
    protected function getTemplateName(string $method) : string
    {
        if ($this->template) {
            return $this->template;
        }

        $template = preg_replace('/([A-Z])/', '-$1', $method);
        $template = strtolower($template);

        return $template;
    }

    /**
     * Sets the name of the template to render
     * @param string $template The name of the template
     * @return static
     */
    protected function setTemplateName(string $template) : static
    {
        $this->template = $template;

        return $this;
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
}
