<?php
/**
* The Request Class
* @package Mars
*/

namespace Mars;

use Mars\Request\Input;
use Mars\Request\Get;
use Mars\Request\Post;
use Mars\Request\Request as RequestObj;
use Mars\Request\Cookie;
use Mars\Request\Server;
use Mars\Request\Env;
use Mars\Request\Files;

/**
 * The Request Class
 * Class handling the $_GET/$_POST/$_COOKIE/$_UPLOAD/$_SERVER interactions
 */
class Request
{
    use AppTrait;

    /**
     * @var string $method The request method. get/post.
     */
    public readonly string $method;

    /**
     * @var Get $get Object containing the get data
     */
    public Get $get;

    /**
     * @var Post $post Object containing the post data
     */
    public Post $post;

    /**
     * @var RequestObj $request Object containing the request data
     */
    public RequestObj $request;

    /**
     * @var RequestObj $request Alias for $request
     */
    public RequestObj $all;

    /**
     * @var Cookie $cookie Object containing the cookie data
     */
    public Cookie $cookie;

    /**
     * @var Server $server Object containing the server data
     */
    public Server $server;

    /**
     * @var Env $env Object containing the env data
     */
    public Env $env;

    /**
     * @var Files $files Object containing the files data
     */
    public Files $files;

    /**
     * @var Input $input The default input object to use when get() is called
     */
    protected Input $input;

    /**
     * Builds the request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->method = $this->getMethod();
        
        $this->get = new \Mars\Request\Get($this->app);
        $this->post = new \Mars\Request\Post($this->app);
        $this->request = new \Mars\Request\Request($this->app);
        $this->cookie = new \Mars\Request\Cookie($this->app);
        $this->server = new \Mars\Request\Server($this->app);
        $this->env = new \Mars\Request\Env($this->app);
        $this->files = new \Mars\Request\Files($this->app);
        $this->all = $this->request;

        if ($this->method == 'post') {
            $this->input = $this->post;
        } else {
            $this->input = $this->get;
        }
    }

    /**
     * Returns the request method: get/post/put
     * @return string
     */
    protected function getMethod() : string
    {
        if ($this->app->is_bin) {
            return '';
        }

        return strtolower($_SERVER['REQUEST_METHOD']);
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
    public function getAction(string $action_param = null) : string
    {
        $action_param = $action_param ?? $this->app->config->request_action_param;

        return $this->all->get($action_param);
    }

    /**
     * Gets the order by value
     * @param array $valid_fields Array containing the valid values
     * @param string $default_field The default field. It will be returned if $valid_fields are specified and none match
     * @param string $orderby_param The name of the orderby param
     * @return string The 'order by' value
     */
    public function getOrderBy(array $valid_fields = [], string $default_field = '', string $orderby_param = null) : string
    {
        $orderby_param = $orderby_param ?? $this->app->config->request_orderby_param;

        $orderby = $this->all->get($orderby_param);

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

        $order = strtolower($this->all->get($order_param));

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
    public function getPage(string $page_param = null) : int
    {
        $page_param = $page_param ?? $this->app->config->request_page_param;

        return $this->all->get($page_param, 'absint');
    }
}
