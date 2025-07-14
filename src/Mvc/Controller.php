<?php
/**
* The Controller Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\App\Kernel;
use Mars\HiddenProperty;
use Mars\Config;
use Mars\Hidden;
use Mars\Escape;
use Mars\Filter;
use Mars\Http\Request;
use Mars\Url;
use Mars\Validator;
use Mars\System\Plugins;
use Mars\Alerts\Errors;
use Mars\Alerts\Messages;
use Mars\Alerts\Info;
use Mars\Alerts\Warnings;
use Mars\Extensions\Extension;

/**
 * The Controller Class
 * Implements the Controller functionality of the MVC pattern
 */
abstract class Controller extends \stdClass
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

            $this->default_method = $this->app->getMethod($this->parent->name ?? 'index');

            return $this->default_method;
        }
    }

    /**
     * @var string $default_success_method Method to be executed on dispatch/route, if the requested method returns true
     */
    public string $default_success_method {
        get {
            if (isset($this->default_success_method)) {
                return $this->default_success_method;
            }

            $this->default_success_method = $this->default_method;

            return $this->default_success_method;
        }
    }

    /**
     * @var string $default_error_method Method to be executed on dispatch/route, if the requested method returns false
     */
    public string $default_error_method {
        get {
            if (isset($this->default_error_method)) {
                return $this->default_error_method;
            }

            $this->default_error_method = $this->default_method;

            return $this->default_error_method;
        }
    }

    /**
     * @var string $current_method The name of the currently executed method
     */
    public string $current_method = '';

    /**
     * @var string $path The controller's parents's dir. Alias for $this->parent->path
     */
    public protected(set) string $path {
        get {
            if (isset($this->path)) {
                return $this->path;
            }

            $this->path = $this->parent ? $this->parent->path : '';
            
            return $this->path;
        }
    }

    /**
     * @var string $assets_path The folder where the assets files are stored
     */
    public protected(set) string $assets_path {
        get {
            if (isset($this->assets_path)) {
                return $this->assets_path;
            }

            $this->assets_path = $this->parent ? $this->parent->assets_path : '';

            return $this->assets_path;
        }
    }

    /**
     * @var string $assets_url The url pointing to the folder where the assets for the extension are located
     */
    public protected(set) string $assets_url {
        get {
            if (isset($this->assets_url)) {
                return $this->assets_url;
            }

            $this->assets_url = $this->parent ? $this->parent->assets_url : '';

            return $this->assets_url;
        }
    }

    /**
     * @var Extension $parent The parent extension
     */
    public protected(set) ?Extension $parent;

    /**
     * @var object $model The model object
     */
    public protected(set) ?object $model {
        get {
            if (isset($this->model)) {
                return $this->model;
            }
            
            $this->model = null;
            if ($this->load_model) {
                $this->model = $this->getModel();
            }

            return $this->model;
        }
    }

    /**
     * @var View $view The view object
     */
    public protected(set) ?View $view {
        get {
            if (isset($this->view)) {
                return $this->view;
            }

            $this->view = null;
            if ($this->load_view) {
                $this->view = $this->getView();
            }

            return $this->view;
        }
    }

    /**
     * @var bool $accept_json Whether the controller can return json data
     */
    protected bool $accept_json = false;

    /**
     * @var Config $config The config object. Alias for $this->app->config
     */
    #[HiddenProperty]
    protected Config $config {
        get => $this->app->config;
    }

    /**
     * @var Filter $filter The filter object. Alias for $this->app->filter
     */
    #[HiddenProperty]
    protected Filter $filter {
        get => $this->app->filter;
    }

    /**
     * @var Escape $escape Alias for $this->app->escape
     */
    #[HiddenProperty]
    protected Escape $escape {
        get => $this->app->escape;
    }

    /**
     * @var Request $request The request object. Alias for $this->app->request
     */
    #[HiddenProperty]
    protected Request $request {
        get => $this->app->request;
    }

    /**
     * @var Validator $url Alias for $this->app->url
     */
    #[HiddenProperty]
    protected Url $url {
        get => $this->app->url;
    }

    /**
     * @var Validator $validator Alias for $this->app->validator
     */
    #[HiddenProperty]
    protected Validator $validator {
        get => $this->app->validator;
    }

    /**
     * @var Plugins $plugins Alias for $this->app->plugins
     */
    #[HiddenProperty]
    protected Plugins $plugins {
        get => $this->app->plugins;
    }

    /**
     * @var Errors $errors The errors object. Alias for $this->app->errors
     */
    protected Errors $errors {
        get => $this->app->errors;
    }

    /**
     * @var Messages $messages The messages object. Alias for $this->app->messages
     */
    protected Messages $messages {
        get => $this->app->messages;
    }

    /**
     * @var Info $info The info object. Alias for $this->app->info
     */
    protected Info $info {
        get => $this->app->info;
    }

    /**
     * @var Warnings $warnings The warnings object. Alias for $this->app->warnings
     */
    protected Warnings $warnings {
        get => $this->app->warnings;
    }

    /**
     * @var bool $load_model If true, will automatically load the model
     */
    protected bool $load_model = true;

    /**
     * @var bool $load_voew If true, will automatically load the view
     */
    protected bool $load_view = true;

    /**
     * @var array $validation_rules Validation rules
     */
    protected static array $validation_rules = [];

    /**
     * Builds the controller
     * @param Extension $parent The parent extension
     * @param App $app The app object
     */
    public function __construct(?Extension $parent = null, ?App $app = null)
    {
        $this->app = $app ?? App::obj();
        $this->parent = $parent;

        $this->init();
    }

    /**
     * Inits the controller. Method which can be overriden in custom controllers to init the models/views etc..
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
     * Sets the default_success_method and default_error_method to the same method
     * @param string $method The name of the method
     * @return static
     */
    public function setDefaultMethods(string $method) : static
    {
        $this->default_success_method = $method;
        $this->default_error_method = $method;

        return $this;
    }

    /**
     * Loads the model and returns the instance
     * @param string $model The name of the model
     * @return object The model
     */
    public function getModel(string $model = '') : object
    {
        return $this->parent->getModel($model, $this);
    }

    /**
     * Loads the view and returns the instance
     * @param string $view The name of the view
     * @return View The view
     */
    public function getView(string $view = '') : View
    {
        return $this->parent->getView($view, $this);
    }

    /**
     * Calls method $method.
     * Calls it only if it exists and it's public. If not will call the $default_method method.
     * If the method returns true, $default_success_method will be called afterwards.
     * If it returns false, $default_error_method is called.
     * No method is called, if the method doesn't return a value
     * @param string $method The name of the method
     * @param array $params Params to be passed to the method, if any
     */
    public function dispatch(string $method = '', array $params = [])
    {
        if (!$method) {
            $method = $this->app->request->getAction();
            if (!$method) {
                $method = $this->default_method;
            }
        }

        $method = App::getMethod($method);

        if (method_exists($this, $method)) {
            if ($this->canDispatch($method)) {
                $this->route($method, $params);
                return;
            }
        } elseif (isset($this->$method)) {
            //call a dynamic added method,if any
            if ($this->$method instanceof \Closure) {
                call_user_func_array($this->$method, [$this]);
                return;
            }
        }

        //call the default method
        $this->route($this->default_method);
    }

    /**
     * Calls method $method, if it's callable, then the default_success/error_method based on what value the method returns.
     * If the method returns nothing no additional method is called
     * @param string $method The name of the method
     * @param array $params Params to be passed to the method, if any
     */
    protected function route(string $method, array $params = [])
    {
        $ret = $this->call($method, $params);
        $is_json = ($this->accept_json && $this->app->request->is_json) ? true : false;

        //if the request is json, send the return data as json, if it is not a boolean or null
        if ($is_json) {
            $data = [];
            if (!is_bool($ret) && !is_null($ret)) {
                $data['content'] = $ret;
            }

            $this->app->send($data);
            return;
        }

        //call the success/error methods if the first call returns true or false
        if ($ret === true) {
            $this->call($this->default_success_method);
        } elseif ($ret === false) {
            $this->call($this->default_error_method);
        } elseif (is_string($ret)) {
            //output the return data as html code
            $this->app->output($ret);
        } elseif (is_array($ret) || is_object($ret)) {
            //output the return data as json code
            $this->app->send($ret);
        }
    }

    /**
     * Calls a method of the controller
     * @param string $method The name of the method
     * @return mixed Returns whatever $method returns
     * @param array $params Params to be passed to the method, if any
     * @return mixed
     */
    protected function call(string $method, array $params = [])
    {
        $this->current_method = $method;

        return call_user_func_array([$this, $method], $params);
    }

    /**
     * Checks if the $method can be called
     * @param string $method The name of the method
     * @return bool
     */
    protected function canDispatch(string $method) : bool
    {
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
     * Alias for $this->view->render()
     */
    protected function render()
    {
        $this->view->render();
    }

    /**
     * Returns true if no errors have been generated
     * @return bool
     */
    protected function success() : bool
    {
        return $this->app->success();
    }

    /**
     * Sends an error as ajax content
     * @param string $error The response error to send
     */
    protected function sendError(string $error)
    {
        $this->app->errors->add($error);

        $this->app->send([]);
    }
}
