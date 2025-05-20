<?php
/**
* The Controller Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Debug\InfoTrait;
use Mars\Hidden;
use Mars\Escape;
use Mars\Filter;
use Mars\Request;
use Mars\Uri;
use Mars\Validator;
use Mars\System\Plugins;
use Mars\Alerts\Errors;
use Mars\Alerts\Messages;
use Mars\Alerts\Info;
use Mars\Alerts\Warnings;
use Mars\Extensions\Extension;
use Mars\Validation\ValidateTrait;

/**
 * The Controller Class
 * Implements the Controller functionality of the MVC pattern
 */
abstract class Controller extends \stdClass
{
    use InstanceTrait;
    use InfoTrait;
    use ValidateTrait {
        validate as protected validateData;
    }

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
     * @var Filter $filter The filter object. Alias for $this->app->filter
     */
    #[Hidden]
    protected Filter $filter {
        get => $this->app->filter;
    }

    /**
     * @var Escape $escape Alias for $this->app->escape
     */
    #[Hidden]
    protected Escape $escape {
        get => $this->app->escape;
    }

    /**
     * @var Request $request The request object. Alias for $this->app->request
     */
    #[Hidden]
    protected Request $request {
        get => $this->app->request;
    }

    /**
     * @var Validator $uri Alias for $this->app->uri
     */
    #[Hidden]
    protected Uri $uri {
        get => $this->app->uri;
    }

    /**
     * @var Validator $validator Alias for $this->app->validator
     */
    #[Hidden]
    protected Validator $validator {
        get => $this->app->validator;
    }

    /**
     * @var Plugins $plugins Alias for $this->app->plugins
     */
    #[Hidden]
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
    protected Warnings $warnings    {
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
        $this->app = $app ?? $this->getApp();
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
        return $this->parent->getModel($model);
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
            $this->sendData($ret);
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
     * Sends $content as ajax content
     * @param array | object $data The data to output
     */
    protected function sendData(array | object $data)
    {
        $this->app->output($this->getData($data), 'ajax');
    }

    /**
     * Returns a response array
     * @param array | object $data The data to output
     * @return array
     */
    protected function getData(array | object $data) : array
    {
        $data_array = ['success'=> true, 'error' => $this->app->errors->getFirst(), 'message' => $this->app->messages->getFirst(), 'warning' => $this->app->warnings->getFirst(), 'info' => $this->app->info->getFirst()];

        if ($this->app->success()) {
            $data_array = $data_array + App::getArray($data);
        } else {
            $data_array['success'] = false;
        }

        return $data_array;
    }

    /**
     * Returns a basic response array, not populated with any values
     * @return array
     */
    protected function getDataArray() : array
    {
        $data = ['success'=> true, 'error' => '', 'message' => '', 'warning' => '', 'info' => '', 'html' => ''];

        return $data;
    }

    /**
     * Sends an error as ajax content
     * @param string $error The response error to send
     */
    protected function sendError(string $error)
    {
        $data = $this->getDataArray();
        $data['success'] = false;
        $data['error'] = $error;

        $this->app->output($data, 'ajax');
    }

    /**
     * Sends an alert
     * @param string $message The response message to send
     * @param string $alert The alert's type
     */
    protected function sendAlert(string $message, string $alert)
    {
        $data = $this->getDataArray();
        $data[$alert] = $message;

        $this->app->output($data, 'ajax');
    }

    /**
     * Sends a message as ajax content
     * @param string $message The response message to send
     */
    protected function sendMessage(string $message)
    {
        $this->sendAlert($message, 'message');
    }

    /**
     * Sends a warning as ajax content
     * @param string $message The response message to send
     */
    protected function sendWarning(string $message)
    {
        $this->sendAlert($message, 'warning');
    }

    /**
     * Sends an info as ajax content
     * @param string $message The response message to send
     */
    protected function sendInfo(string $message)
    {
        $this->sendAlert($message, 'info');
    }
}
