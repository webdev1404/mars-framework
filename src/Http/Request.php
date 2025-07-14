<?php
/**
* The Request Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;
use Mars\LazyLoadProperty;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\Http\Request\Input;
use Mars\Http\Request\Get;
use Mars\Http\Request\Post;
use Mars\Http\Request\Request as RequestObj;
use Mars\Http\Request\Cookies;
use Mars\Http\Request\Server;
use Mars\Http\Request\Env;
use Mars\Http\Request\Files;

/**
 * The Request Class
 * Class handling the $_GET/$_POST/$_COOKIE/$_UPLOAD/$_SERVER interactions
 */
class Request
{
    use Kernel;
    use LazyLoad;

    /**
     * @var RequestObj $request Alias for $request
     */
    public RequestObj $all {
        get => $this->request;
    }

    /**
     * @var RequestObj $request Object containing the request data
     */
    #[LazyLoadProperty]
    public RequestObj $request;

    /**
     * @var Get $get Object containing the get data
     */
    #[LazyLoadProperty]
    public Get $get;

    /**
     * @var Post $post Object containing the post data
     */
    #[LazyLoadProperty]
    public Post $post;

    /**
     * @var Cookies $cookies Object containing the cookie data
     */
    #[LazyLoadProperty]
    public Cookies $cookies;

    /**
     * @var Server $server Object containing the server data
     */
    #[LazyLoadProperty]
    public Server $server;

    /**
     * @var Env $env Object containing the env data
     */
    #[LazyLoadProperty]
    public Env $env;

    /**
     * @var Files $files Object containing the files data
     */
    #[LazyLoadProperty]
    public Files $files;

    /**
     * @var Input $input The default input object to use when get() is called
     */
    public Input $input {
        get {
            if (isset($this->input)) {
                return $this->input;
            }

            $this->input = $this->get;
            if ($this->method == 'post') {
                $this->input = $this->post;
            }

            return $this->input;
        }
    }

    /**
     * @var string $method The request method. get/post.
     */
    public protected(set) string $method {
        get {
            if (isset($this->method)) {
                return $this->method;
            }

            $this->method = '';
            if ($this->app->is_web) {
                $this->method = strtolower($_SERVER['REQUEST_METHOD']);
            }

            return $this->method;
        }
    }

    /**
     * @var bool $is_post Whether the request is a post request
     */
    public protected(set) bool $is_post {
        get {
            if (isset($this->is_post)) {
                return $this->is_post;
            }

            $this->is_post = false;
            if ($this->method == 'post') {
                $this->is_post = true;
            }

            return $this->is_post;
        }
    }

    /**
     * @var bool $is_json Whether the request is a json request
     */
    public protected(set) bool $is_json {
        get {
            if (isset($this->is_json)) {
                return $this->is_json;
            }

            $this->is_json = false;
            if (isset($_SERVER['HTTP_ACCEPT'])) {
                if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    $this->is_json = true;
                }
            }

            return $this->is_json;
        }
    }

    /**
     * Builds the request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;
    }

    /**
     * Returns the value of a variable from either $_GET or $_POST
     * Shorthand for $this->request->get()
     * @param string $filter The filter to apply to the value, if any. See class Filter for a list of filters
     * @param mixed $default_value The default value to return if the variable is not set
     * @param bool $is_array Whether the value should be returned as an array
     * @return mixed The value
     */
    public function get(string $name, string $filter = '', mixed $default_value = '', bool $is_array = false) : mixed
    {
        return $this->input->get($name, $filter, $default_value, $is_array);
    }
    /**
     * Returns all the request data from either $_GET or $_POST
     * Shorthand for $this->request->getAll()
     * @return array
     */
    public function getAll() : array
    {
        return $this->input->getAll();
    }

    /**
     * Returns the action to be performed
     * @param string $action_param The action param
     * @return string The action
     */
    public function getAction(string $action_param = '') : string
    {
        $action_param = $action_param ? $action_param : $this->app->config->request_action_param;

        return $this->request->get($action_param);
    }

    /**
     * Gets the order by value
     * @param array $valid_fields Array containing the valid values
     * @param string $default_field The default field. It will be returned if $valid_fields are specified and none match
     * @param string $orderby_param The name of the orderby param
     * @return string The 'order by' value
     */
    public function getOrderBy(array $valid_fields = [], string $default_field = '', string $orderby_param = '') : string
    {
        $orderby_param = $orderby_param ? $orderby_param : $this->app->config->request_orderby_param;

        $orderby = $this->request->get($orderby_param);

        if ($valid_fields) {
            if (array_is_list($valid_fields)) {
                if (in_array($orderby, $valid_fields)) {
                    return $orderby;
                }
            } else {
                if (isset($valid_fields[$orderby])) {
                    return $valid_fields[$orderby];
                }
            }

            return $default_field;
        }

        return $orderby;
    }

    /**
     * Returns the order value
     * @param string $order_param The name of the order param
     * @return string The order value; asc/desc
     */
    public function getOrder(string $order_param = 'order') : string
    {
        $order_param = $order_param ?? $this->app->config->request_order_param;

        $order = strtolower($this->request->get($order_param));

        if ($order == 'asc') {
            return 'ASC';
        } elseif ($order == 'desc') {
            return 'DESC';
        }

        return '';
    }

    /**
     * Gets the current page of the pagination system
     * @param string $page_param The name of the page param
     * @return int The value of the current page
     */
    public function getPage(string $page_param = '') : int
    {
        $page_param = $page_param ? $page_param : $this->app->config->request_page_param;

        return $this->request->get($page_param, 'absint', 0);
    }
}
