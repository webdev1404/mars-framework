<?php
/**
* The Routes Class
* @package Mars
*/

namespace Mars\Router;

use Mars\App\Kernel;
use Mars\Extensions\Modules\Block;
use Mars\Content\Page;
use Mars\Content\Template;

/**
 * The Routes Class
 * Routes storage and handling class
 */
class Routes
{
    use Kernel;

    /**
     * @var array $routes_list The defined routes list
     */
    public protected(set) array $routes_list = [];

    /**
     * @var array $allowed_methods The allowed request methods
     */
    public protected(set) array $allowed_methods = ['get', 'post', 'put', 'delete'];

    /**
     * @var string $current_filename The current filename being loaded
     */
    protected string $current_filename = '';

    /**
     * @var array $hashes If specified, will load only this route hashes
     */
    protected array $hashes = [];

    /**
     * @var bool $load_action If true, will load the action for the route
     */
    protected bool $load_action = false;

    /**
     * Resets the routes list
     */
    public function reset() : static
    {
        $this->routes_list = [];

        return $this;
    }

    /**
     * Loads the routes from a file
     * @param string $filename The file to load the routes from
     */
    public function load(string $filename) : static
    {
        $app = $this->app;
        $router = $this;

        $this->current_filename = $filename;
        require($filename);

        return $this;
    }

    /**
     * Handles both get and post requests
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function all(string $route, $action, string|array|null $languages = null) : static
    {
        $this->add($this->allowed_methods, $route, $action, $languages);

        return $this;
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function get(string $route, $action, string|array|null $languages = null) : static
    {
        return $this->add('get', $route, $action, $languages);
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @returnstatic
     */
    public function post(string $route, $action, string|array|null $languages = null) : static
    {
        return $this->add('post', $route, $action, $languages);
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function put(string $route, $action, string|array|null $languages = null) : static
    {
        return $this->add('put', $route, $action, $languages);
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function delete(string $route, $action, string|array|null $languages = null) : static
    {
        return $this->add('delete', $route, $action, $languages);
    }

    /**
     * Handles multiple routes for a specific language
     * @param string $language The language code
     * @param array $routes The routes to handle
     * @param array $methods The request methods to handle. Default: get
     * @return static
     */
    public function lang(string $language, array $routes, array $methods = ['get']) : static
    {
        foreach ($routes as $route => $action) {
            $this->add($methods, $route, [$language => $action], $language);
        }

        return $this;
    }

    /**
     * Handles a block request
     * @param string $route The route to handle
     * @param string $module_name The module the block belongs to
     * @param string $name The block's name
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $params The params to pass to the block, if any
     * @param array $methods The request methods to handle. Default: get, post
     * @return static
     */
    public function block(string $route, string $module_name, string $name = '', string|array|null $languages = null, array $params = [], array $methods = ['get', 'post']) : static
    {
        $handler = fn () => new Block($module_name, $name, $params, $this->app);

        return $this->add($methods, $route, $handler, $languages);
    }
    
    /**
     * Handles a template request
     * @param string $route The route to handle
     * @param string $template The template's name
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string $title The title tag of the page
     * @param array $meta Meta data of the page
     * @param array $methods The request methods to handle. Default: get
     * @return static
     */
    public function template(string $route, string $template, string|array|null $languages = null, string $title = '', array $meta = [], array $methods = ['get']) : static
    {
        $handler = fn () => new Template($template, $title, $meta, $this->app);

        return $this->add($methods, $route, $handler, $languages);
    }

    /**
     * Handles a page request
     * @param string $route The route to handle
     * @param string $template The page's template's name
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string $title The title tag of the page
     * @param array $meta Meta data of the page
     * @param array $methods The request methods to handle. Default: get
     * @return static
     */
    public function page(string $route, string $template, string|array|null $languages = null, string $title = '', array $meta = [], array $methods = ['get']) : static
    {
        $handler = fn () => new Page($template, $title, $meta, $this->app);

        return $this->add($methods, $route, $handler, $languages);
    }

    /**
     * Removes a route
     * @param string|array $route The route to remove
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string|array $methods The request methods to remove. Default: all methods
     * @return static
     */
    public function remove(string|array $route, string|array|null $languages = '*', string|array $methods = []) : static
    {
        $routes = $this->app->array->get($route);

        foreach ($routes as $route) {
            $hashes = $this->getHashes($methods, $route, 'wwwww', $languages);

            foreach ($hashes as $hash => $route) {
                unset($this->routes_list[$hash]);
            }
        }

        return $this;
    }

    /**
     * Adds a route
     * @param string $method The method: get/post/put/delete
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string or a controller. If array will register multiple routes
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    protected function add(string|array $method, string $route, $action, string|array|null $languages = null) : static
    {
        $this->routes_list = array_merge($this->routes_list, $this->getHashes($method, $route, $action, $languages));

        return $this;
    }

    /**
     * Returns the hashes for a route
     * @param string $method The method: get/post/put/delete
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string or a controller. If array will register multiple routes
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    protected function getHashes(string|array $method, string $route, $action, string|array|null $languages = null) : array
    {
        $hashes = [];
        $methods = $this->getMethods($method);
        $route = $this->getName($route);
        $languages = $this->getLanguages($languages);

        foreach ($methods as $method) {
            $method = strtolower($method);
            if (!in_array($method, $this->allowed_methods)) {
                throw new \Exception('Invalid route method: ' . $method);
            }

            foreach ($languages as $language) {
                $action_list = is_array($action) ? $action : [$language => $action];

                foreach ($action_list as $lang => $act) {
                    $hash = $this->getHash($method, $route, $lang);

                    //only loads the $this->hashes, if specified
                    if ($this->hashes && !isset($this->hashes[$hash])) {
                        continue;
                    }

                    $data = ['route' => $route, 'filename' => $this->current_filename];
                    if ($this->load_action) {
                        $data['action'] = $act;
                    }

                    $hashes[$hash] = $data;
                }
            }
        }

        return $hashes;
    }

    /**
     * Cleans the route name
     * @param string $route The route to clean
     * @return string The cleaned route name
     */
    protected function getName(string $route) : string
    {
        //strip the leading slash, for all the routes except the root
        if ($route != '/') {
            $route = ltrim($route, '/');
        }

        return $route;
    }

    /**
     * Returns the hash for a route
     */
    protected function getHash(string $method, string $route, string $language) : string
    {
        return md5($route) . md5($method . $language);
    }

    /**
     * Returns the methods to use for a route
     * @param string|array $method The method or methods to use
     * @return array The list of methods
     */
    protected function getMethods(string|array $method) : array
    {
        $methods = [];
        if (!$method) {
            return $this->allowed_methods;
        }

        return $this->app->array->get($method);
    }

    /**
     * Returns the languages to use for a route
     * @param string|array|null $languages The languages to use
     * @return array The list of languages
     */
    protected function getLanguages(string|array|null $languages = null) : array
    {
        if ($languages === null) {
            return [$this->app->lang->default_code];
        } elseif (is_array($languages)) {
            return $languages;
        }

        if ($languages == '*') {
            return $this->app->lang->codes;
        }

        return [$languages];
    }
}
