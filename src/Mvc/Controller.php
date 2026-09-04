<?php
/**
* The Controller Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\App\HiddenProperty;
use Mars\Config;
use Mars\Hidden;
use Mars\Escape;
use Mars\Filter;
use Mars\Http\Request;
use Mars\Http\Request\Get;
use Mars\Http\Request\Post;
use Mars\Mail;
use Mars\Validator;
use Mars\System\Plugins;
use Mars\System\Uri;
use Mars\Alerts\Errors;
use Mars\Extensions\Extension;
use Mars\Mvc\Controller\Email;
use Mars\Mvc\Controller\Route;
use Mars\Http\Response\Body\Data\Data;
use Mars\Http\Response\Body\Data\Json;

/**
 * The Controller Class
 * Implements the Controller functionality of the MVC pattern
 */
abstract class Controller extends \stdClass
{
    use Kernel;
    use LazyLoad;

    /**
     * @var string $name The name of the controller
     */
    public protected(set) string $name {
        get {
            if (isset($this->name)) {
                return $this->name;
            }

            $this->name = basename(str_replace('\\', '/', static::class));

            return $this->name;
        }
    }

    /**
     * @var string $default_method Default method to be executed on dispatch/route or if the requested method doesn't exist or is not public
     */
    public protected(set) string $default_method = 'default';

    /**
     * @var string $current_method The name of the currently executed method
     */
    public protected(set) string $current_method = '';

    /**
     * @var array $targets The target methods to be called based on the return value of the method
     */
    public protected(set) array $targets = [];

    /**
     * @var string $path The controller's parent's dir. Alias for $this->parent->path
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
     * @var string $model_class The class name of the model. If set, the model is automatically loaded and assigned to $this->model
     */
    public protected(set) string $model_class = '';

    /**
     * @var ?object $model The model object
     */
    public protected(set) ?object $model {
        get {
            if (isset($this->model)) {
                return $this->model;
            }

            $class_name = $this->model_class ?: Model::class;
            
            $this->model = new $class_name($this, $this->app);

            return $this->model;
        }
    }

    /**
     * @var string $view_class The class name of the view. If set, the view is automatically loaded and assigned to $this->view
     */
    public protected(set) string $view_class = '';

    /**
     * @var View $view The view object
     */
    public protected(set) ?View $view {
        get {
            if (isset($this->view)) {
                return $this->view;
            }

            $class_name = $this->view_class ?: View::class;

            $this->view = new $class_name($this, $this->app);

            return $this->view;
        }
    }

    /**
     * @var bool $accept_json Whether the controller can return json data
     */
    public protected(set) bool $accept_json = false;

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
     * @var Get $get Alias for $this->app->request->get
     */
    #[HiddenProperty]
    protected Get $get {
        get => $this->app->request->get;
    }

    /**
     * @var Post $post Alias for $this->app->request->post
     */
    #[HiddenProperty]
    protected Post $post {
        get => $this->app->request->post;
    }

    /**
     * @var Uri $url Alias for $this->app->url
     */
    #[HiddenProperty]
    protected Uri $url {
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
     * @var Mail $mail The mail object. Alias for $this->app->mail
     */
    #[HiddenProperty]
    protected Mail $mail {
        get => $this->app->mail;
    }

    /**
     * @var Email $email The email object
     */
    #[LazyLoadProperty]
    protected Email $email;

    /**
     * @var Errors $errors The generated errors
     */
    public protected(set) Errors $errors {
        get {
            if (isset($this->errors)) {
                return $this->errors;
            }

            $this->errors = new Errors($this->app);

            return $this->errors;
        }
    }

    /**
     * @internal
     */
    protected static array $lazyload_add_this = [
       Email::class
    ];

    /**
     * Builds the controller
     * @param Extension $parent The parent extension
     * @param App $app The app object
     */
    public function __construct(?Extension $parent = null, ?App $app = null)
    {
        $this->parent = $parent;
        $this->app = $app;

        $this->lazyLoad($this->app);

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
     * Shows the flashes messages, if any
     */
    protected function flashes()
    {
        $this->app->session->flashes();
    }

    /**
     * Redirects to the given url with an optional message
     * @param string|null $url The url to redirect to
     * @param string $type The type of the alert (error, success, info, warning)
     * @param string $alert The alert message
     */
    protected function redirect(?string $url, string $type = '', string $alert = '')
    {
        if ($type && $alert) {
            $this->app->session->flash($type, $alert);
        }

        $this->app->redirect($url);
    }

    /**
     * Creates a route object for the given method
     * @param string $method The name of the method
     * @return Route The route object
     */
    protected function go(string $method) : Route
    {
        return new Route($method);
    }

    /**
     * Methods to be executed before the dispatch of the controller. Can be overriden in custom controllers
     */
    protected function before()
    {
    }

    /**
     * Calls method $method.
     * Calls it only if it exists and it's public. If not will call the $default_method method.
     * If the method returns bool or Route, then the target method is determined based on the return value.
     * @param string $method The name of the method
     * @param array $params Params to be passed to the method, if any
     * @return Data The response data generated by the method, if any
     */
    public function dispatch(string $method = '', array $params = []) : Data
    {
        if (!$method) {
            $method = $this->app->request->getAction();
            if (!$method) {
                $method = $this->default_method;
            }
        }

        $this->before();

        $method = App::getMethod($method);

        if (method_exists($this, $method)) {
            if ($this->canDispatch($method)) {
                return $this->route($method, $params);
            }
        }

        //check if the default method exists
        if (!method_exists($this, $this->default_method)) {
            throw new \Exception("Neither the '{$method}()' method nor the default method '{$this->default_method}()' exist in controller {$this->name}");
        }

        //call the default method
        return $this->route($this->default_method);
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
     * Calls method $method, if it's callable, then the default_success/error_method based on what value the method returns.
     * If the method returns nothing no additional method is called
     * @param string $method The name of the method
     * @param array $params Params to be passed to the method, if any
     * @return array The response data generated by the method
     */
    protected function route(string $method, array $params = []) : Data
    {
        $data = $this->call($method, params: $params);

        //call the target method if the first call returns true or false or Route
        if (!$data instanceof Data) {
            [$returned, $extra_content] = $data;
            
            $data = $this->call($this->getTarget($method, $returned), $extra_content);
        }

        return $data;
    }

    /**
     * Calls a method of the controller
     * @param string $method The name of the method
     * @param string $extra_content Extra content to be added to the response body, if any
     * @param array $params Params to be passed to the method, if any
     * @return array|Route|Data The return value and content generated by the method
     */
    protected function call(string $method, string $extra_content = '', array $params = []) : array|Route|Data
    {
        $this->current_method = $method;

        ob_start();
        $returned = call_user_func_array([$this, $method], $this->app->reflection->getParams([$this, $method], $params));
        $content = $extra_content . ob_get_clean();

        $is_json = $this->accept_json && $this->app->request->is_json;
        if ($is_json) {
            return $this->getJson($returned, $content);
        }

        if (is_bool($returned) || $returned instanceof Route) {
            return [$returned, $content];
        }

        return $this->app->response->body->create($returned, $content);
    }

    /**
     * Returns the target method to be called, based on the return value of the $method
     * @param string $method The name of the method
     * @param bool|Route $data The return value of the method
     * @return string The name of the method to be called next
     */
    protected function getTarget(string $method, bool|Route $data) : string
    {
        if ($data instanceof Route) {
            return $data->method;
        }

        if (!isset($this->targets[$method])) {
            return $this->default_method;
        }

        $target = $this->targets[$method];
        if (is_array($target)) {
            [$success, $error] = $target;

            return $data ? $success : $error;
        } else {
            return $target;
        }
    }

    /**
     * Gets json data
     * @param mixed $returned The return value
     * @param string $content The content
     * @return array The json data
     */
    protected function getJson(mixed $returned, string $content) : Data
    {
        $data = [];

        if (is_bool($returned) || is_null($returned)) {
            $data = $content;
        } else {
            $data = $returned;
        }

        return new Json($data);
    }

    /**
     * Checks if the request can post data, based on throttle settings
     * @param bool $captcha Whether to check the captcha, if enabled
     * @param string|null $key The throttle key. If null, no throttling is applied
     * @param int|null $max_attempts The max attempts allowed within the duration. If null, no throttling is applied
     * @param int|null $duration The duration in seconds for which the attempts are counted. If null, no throttling is applied
     * @param bool $all Whether to throttle all post requests with the same key. If false, $app->throttle->hit() needs to be called manually
     * @param bool $add_ip Whether to append the user's IP to the key
     * @return bool True if the request can post, false otherwise
     */
    public function canPost(bool $captcha = true, ?string $key = null, ?int $max_attempts = null, ?int $duration = null, bool $all = true, bool $add_ip = true) : bool
    {
        if ($key && $add_ip) {
            $key .= '-' . $this->app->ip;
        }

        return $this->request->canPost($captcha, $key, $max_attempts, $duration, $all);
    }

    /**
     * Validates the post data based on the given rules
     * @param array $rules The rules to validate, in the format ['field' => validation_type]. Eg: 'my_id' => 'required|min:3|unique:my_table:my_id'
     * @param array $error_strings Custom error strings, if any
     * @param array $skip_array Array with the fields for which we'll skip validation, if any
     * @return bool True if the validation passed all tests, false otherwise
     */
    public function validate(array $rules, array $error_strings = [], array $skip_array = []) : bool
    {
        if (!$this->validator->validate($rules, $this->post->data, $error_strings, $skip_array)) {
            $this->errors->set($this->validator->errors);

            return false;
        }

        return true;
    }

    /**
     * The default method
     */
    public function default()
    {
    }
}
