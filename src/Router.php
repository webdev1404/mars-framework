<?php
/**
* The Route Class
* @package Mars
*/

namespace Mars;

use Mars\App\LazyLoad;
use Mars\Router\Base;
use Mars\Router\Loader;
use Mars\Router\Handler;
use Mars\Content\ContentInterface;
use Mars\Mvc\Controller;

/**
 * The Router Class
 * Route handling class
 */
class Router extends Base
{
    use LazyLoad;

    /**
     * @var string $route The route used by the router
     */
    public protected(set) string $route {
        get {
            if (isset($this->route)) {
                return $this->route;
            }

            $this->route = $this->app->url->path;

            //remove the slashes, for all the routes except the root
            if ($this->route) {
                $this->route = trim($this->route, '/');
            } else {
                $this->route = '/';
            }

            return $this->route;
        }
    }

    /**
     * @var Loader $loader The routes loader
     */
    #[LazyLoadProperty]
    public protected(set) Loader $loader;

    /**
     * @var Handler $handler The routes handler
     */
    #[LazyLoadProperty]
    public protected(set) Handler $handler;

    /**
     * The constructor
     * @param App|null $app The app object
     */
    public function __construct(?App $app = null)
    {
        parent::__construct($app);

        $this->lazyLoad($this->app);
    }

    /**
     * Outputs the content based on the matched route
     */
    public function execute()
    {
        $route = $this->getRoute();
        if (!$route) {
            //try to get the 404 route, if exists
            $route = $this->getRoute('404');

            if (!$route) {
                $this->notFound();
            }
        }
        
        $this->output($route);
    }

    /**
     * Handles the 404 not found cases
     */
    public function notFound()
    {
        header('HTTP/1.0 404 Not Found', true, 404);
        die;
    }

    /**
     * Returns the route matching the current request
     * @param string|null $route The route to get the route for. If null, the current route will be used
     * @return array|null
     */
    protected function getRoute(?string $route = null) : array|null
    {
        //check if the method is allowed
        if (!in_array($this->app->request->method, static::ALLOWED_METHODS)) {
            return null;
        }

        $route ??= $this->route;
        $hash = $this->getHash($route);
        $hashes = $this->app->cache->routes->getHashes($this->app->request->method, $this->app->lang->code, $this->getPrefix($route));
        if (!$hashes) {
            return null;
        }

        return $this->handler->get($hash, $hashes);
    }

    /**
     * Outputs the content of a route
     * @param array $route The route
     */
    protected function output(array $route)
    {
        [$action, $params] = $route;

        if (is_callable($action)) {
            $this->outputFromClosure($action, $params);
        } elseif (is_object($action)) {
            $this->outputFromObject($action, $params);
        } elseif (is_string($action)) {
            $parts = explode('@', $action);

            $class_name = $parts[0];
            $method = $parts[1] ?? '';

            $this->outputFromClass($class_name, $method, $params);
        }
    }

    /**
     * Outputs the content from a callable
     * @param callable $route The closure to output
     * @param array $params The params to pass to the callable
     */
    protected function outputFromClosure(callable $route, array $params)
    {
        ob_start();
        $value = call_user_func_array($route, [...$this->app->reflection->getParams($route, $params), $this->app]);
        $content = ob_get_clean();

        $this->outputContent($value, $content, $params);
    }

    /**
     * Outputs the content from an object
     * @param object $object The object to output
     */
    protected function outputFromObject(object $object, array $params)
    {
        if ($object instanceof ContentInterface) {
            $object->output($params);
        } else {
            $this->app->send($object);
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
                $value = call_user_func_array([$controller, $method], $this->app->reflection->getParams([$controller, $method], $params));
                $content = ob_get_clean();

                $this->outputContent($value, $content);
            } else {
                throw new \Exception("No controller method to handle the route for class: {$class_name}");
            }
        }
    }

    /**
     * Outputs the returned value and the content
     * @param string|array|object|null $value The value to output
     * @param string $content The content to output
     * @param array $params The params of the route
     */
    protected function outputContent(string|array|object|null $value, string $content, array $params = [])
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
            $this->outputFromObject($value, $params);
        }
    }
}
