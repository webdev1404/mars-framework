<?php
/**
* The Route Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\Mvc\Controller;
use Mars\Extensions\Modules\Block;
use Mars\Content\ContentInterface;
use Mars\Content\Page;
use Mars\Content\Template;

/**
 * The Route Class
 * Route handling class
 */
class Router
{
    use Kernel;

    /**
     * @var array $routes_list The defined routes list
     */
    public protected(set) array $routes_list = [];

    /**
     * @var string $path The path used by the router
     */
    public protected(set) string $path {
        get {
            if (isset($this->path)) {
                return $this->path;
            }

            $this->path = $this->app->url->path;
            //remove the language code from the path, if multi-language is enabled
            if ($this->app->lang->multi) {
                $this->path = str_replace('/' . $this->app->lang->code, '', $this->path);
            }

            //remove the leading slash, for all the routes except the root
            if ($this->path) {
                $this->path = ltrim($this->path, '/');
            } else {
                $this->path = '/';
            }

            return $this->path;
        }
    }
    
    /**
     * Adds a route
     * @param string $type The type: get/post/put/delete
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string or a controller
     * @param string|null $language The language to use for the route, if any
     * @return static
     */
    public function add(string $type, string $route, $action, ?string $language = null) : static
    {
        $language ??= $this->app->lang->default_code;

        $route = $this->cleanRouteName($route);

        $this->routes_list[$type][$route][$language] = $action;

        return $this;
    }

    /**
     * Cleans the route name
     * @param string $route The route to clean
     * @return string The cleaned route name
     */
    protected function cleanRouteName(string $route) : string
    {
        //strip the leading slash, for all the routes except the root
        if ($route != '/') {
            $route = ltrim($route, '/');
        }

        return $route;
    }

    /**
     * Outputs the content based on the matched route
     */
    public function execute()
    {
        $route = $this->getRoute();
        if (!$route) {
            $this->notFound();
        }
        
        $this->output($route);
    }

    /**
     * Outputs the content of a route
     * @param array $route The route
     */
    protected function output($route)
    {
        [$route, $params] = $route;

        if (is_string($route)) {
            $parts = explode('@', $route);

            $method = '';
            $class_name = $parts[0];
            if (isset($parts[1])) {
                $method = $parts[1];
            }

            $this->outputFromClass($class_name, $method, $params);
        } elseif (is_callable($route)) {
            $this->outputFromClosure($route);
        } elseif (is_object($route) || is_array($route)) {
            $this->outputFromObject($route);
        }
    }

    /**
     * Outputs the content from a class
     * @param string $class_name The class name
     * @param string $method The method to call
     * @param array $params The params to pass to the method
     */
    protected function outputFromClass(string $class_name, string $method, array $params)
    {
        $controller = new $class_name;

        if ($controller instanceof Controller) {
            $controller->dispatch($method, $params);
        } else {
            if ($method) {
                ob_start();
                $value = call_user_func_array([$controller, $method], $params);
                $content = ob_get_clean();

                $this->outputContent($value, $content);
            } else {
                throw new \Exception('No controller method to handle the route');
            }
        }
    }

    /**
     * Outputs the content from a closure
     * @param \Closure $route The closure to output
     */
    protected function outputFromClosure(\Closure $route)
    {
        ob_start();
        $value = call_user_func_array($route, [$this->app]);
        $content = ob_get_clean();

        $this->outputContent($value, $content);
    }

    /**
     * Outputs the returned value and the content
     * @param string|array|object|null $value The value to output
     * @param string $content The content to output
     */
    protected function outputContent(string|array|object|null $value, string $content)
    {
        //if nothing was returned, output the content. Otherwise, append the content to the returned value
        if (!$value || is_string($value)) {
            $value = $value ? $content . $value : $content;
        }

        if (is_string($value)) {
            $this->app->output($value);
        } elseif (is_array($value)) {
            $this->app->send($value);
        } elseif (is_object($value)) {
            $this->outputFromObject($value);
        }
    }

    /**
     * Outputs the content from an object
     * @param array|object $object The object to output
     */
    protected function outputFromObject(array|object $object)
    {
        if ($object instanceof ContentInterface) {
            $object->output();
        } else {
            $this->app->send($object);
        }
    }

    /**
     * Returns the route matching the current request
     * @return mixed
     */
    protected function getRoute()
    {
        $method = $this->app->request->method;
        if (!isset($this->routes_list[$method])) {
            return null;
        }

        $routes = $this->routes_list[$method];
        foreach ($routes as $route_path => $routes_array) {
            //get the route params
            $params = [];
            $params_keys = [];
            $route_path = preg_quote($route_path, '|');

            $route_path = preg_replace_callback('/\\\{([a-z0-9_]*)\\\}/is', function ($match) use (&$params_keys) {
                $params_keys[] = $match[1];

                return '(.*)';
            }, $route_path);

            if (preg_match("|^{$route_path}\/?$|is", $this->path, $matches)) {
                foreach ($matches as $key => $val) {
                    if (!$key) {
                        continue;
                    }

                    $param_key = $params_keys[$key - 1];

                    $params[$param_key] = $val;
                }

                $route = $routes_array[$this->app->lang->code] ?? ($routes_array[$this->app->lang->default_code] ?? null);

                if ($route) {
                    return [$route, $params];
                }
            }
        }

        return null;
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|null $language The language to use for the route, if any
     * @return static
     */
    public function get(string $route, $action, ?string $language = null) : static
    {
        return $this->add('get', $route, $action, $language);
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|null $language The language to use for the route, if any
     * @returnstatic
     */
    public function post(string $route, $action, ?string $language = null) : static
    {
        return $this->add('post', $route, $action, $language);
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|null $language The language to use for the route, if any
     * @return static
     */
    public function put(string $route, $action, ?string $language = null) : static
    {
        return $this->add('put', $route, $action, $language);
    }

    /**
     * Handles a get request
     * @param string $route The route to handle
     * @param mixed The action. Can be a closure, a string, a controller
     * @param string|null $language The language to use for the route, if any
     * @return static
     */
    public function delete(string $route, $action, ?string $language = null) : static
    {
        return $this->add('delete', $route, $action, $language);
    }

    /**
     * Handles a block request
     * @param string $route The route to handle
     * @param string $module_name The module the block belongs to
     * @param string $name The block's name
     * @param string|null $language The language to use for the route, if any
     * @param array $params The params to pass to the block, if any
     * @return static
     */
    public function block(string $route, string $module_name, string $name = '', ?string $language = null, array $params = []) : static
    {
        $handler = fn () => new Block($module_name, $name, $params, $this->app);

        return $this->setRouteObject($route, $handler, $language);
    }
    
    /**
     * Handles a template request
     * @param string $route The route to handle
     * @param string $template The template's name
     * @param string|null $language The language to use for the route, if any
     * @param string $title The title tag of the page
     * @param array $meta Meta data of the page
     * @return static
     */
    public function template(string $route, string $template, ?string $language = null, string $title = '', array $meta = []) : static
    {
        $handler = fn () => new Template($template, $title, $meta, $this->app);

        return $this->setRouteObject($route, $handler, $language);
    }

    /**
     * Handles a page request
     * @param string $route The route to handle
     * @param string $template The page's template's name
     * @param string|null $language The language to use for the route, if any
     * @param string $title The title tag of the page
     * @param array $meta Meta data of the page
     * @return static
     */
    public function page(string $route, string $template, ?string $language = null, string $title = '', array $meta = []) : static
    {
        $handler = fn () => new Page($template, $title, $meta, $this->app);

        return $this->setRouteObject($route, $handler, $language);
    }
    
    /**
     * Sets the object which will handle the route
     * @param string $route The route to handle
     * @param callable $handler The handler to call when the route is matched
     * @param string|null $language The language to use for the route, if any
     */
    protected function setRouteObject(string $route, callable $handler, ?string $language = null) : static
    {
        $this->add('get', $route, $handler, $language);
        $this->add('post', $route, $handler, $language);

        return $this;
    }

    /**
     * Handles the 404 not found cases
     */
    public function notFound()
    {
        header('HTTP/1.0 404 Not Found', true, 404);
        die;
    }
}
