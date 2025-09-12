<?php
/**
* The Route Class
* @package Mars
*/

namespace Mars;

use Mars\Router\Routes;
use Mars\Content\ContentInterface;
use Mars\Mvc\Controller;

/**
 * The Router Class
 * Route handling class
 */
class Router extends Routes
{
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
     * @internal
     */
    protected bool $load_action = true;

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
     * Handles the 404 not found cases
     */
    public function notFound()
    {
        header('HTTP/1.0 404 Not Found', true, 404);
        die;
    }

    /**
     * Loads a route hash from a file
     * @param string $hash The hash of the route
     * @param string $filename The filename where the route is stored
     */
    protected function loadHash(string $hash, string $filename)
    {
        $this->hashes[$hash] = true;

        $this->load($filename);
    }

    /**
     * Returns the route matching the current request
     * @return mixed
     */
    protected function getRoute() : array|null
    {
        //check if the method is allowed
        if (!in_array($this->app->request->method, $this->allowed_methods)) {
            return null;
        }

        $hashes = $this->app->cache->routes->getHashes($this->path);
        $hash = $this->getHash($this->app->request->method, $this->path, $this->app->lang->code);

        if (isset($hashes[$hash])) {
            return $this->getRouteFromHash($hashes, $hash);
        } else {
            return $this->getRouteFromPreg($hashes);
        }
    }

    /**
     * Returns the route from a hash
     * @param array $hashes The list of hashes
     * @param string $hash The hash to look for
     * @return array|null The route, or null if not found
     */
    protected function getRouteFromHash(array $hashes, string $hash) : array|null
    {
        $this->loadHash($hash, $hashes[$hash]['filename']);
        if (!$this->routes_list || !isset($this->routes_list[$hash])) {
            return null;
        }

        $action = $this->routes_list[$hash]['action'];

        return [$action, []];
    }

    /**
     * Returns the route from a preg match
     * @param array $hashes The list of hashes
     * @return array|null The route, or null if not found
     */
    protected function getRouteFromPreg(array $hashes) : array|null
    {
        $hashes = array_filter($hashes, fn ($route) => $route['preg']);
        $this->hashes = array_fill_keys(array_keys($hashes), true);

        $filenames = array_unique(array_column($hashes, 'filename'));

        foreach ($filenames as $filename) {
            $this->load($filename);
        }

        if (!$this->routes_list) {
            return null;
        }

        //search for the matching preg
        foreach ($this->routes_list as $hash => $route) {
            $route_path = preg_replace_callback('/{([a-z0-9_]*)}/is', function ($match) {
                return '(.*)';
            }, $route['route']);

            if (preg_match("|^{$route_path}$|is", $this->path, $matches)) {
                $params = array_slice($matches, 1);
                
                return [$route['action'], $params];
            }
        }

        return null;
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
            $this->outputFromObject($action);
        } elseif (is_array($action)) {
            $this->outputFromArray($action);
        } elseif (is_string($action)) {
            $parts = explode('@', $action);

            $class_name = $parts[0];
            $method = $parts[1] ?? '';

            $this->outputFromClass($class_name, $method, $params);
        }
    }

    /**
     * Outputs the content from a closure
     * @param \Closure $route The closure to output
     * @param array $params The params to pass to the closure
     */
    protected function outputFromClosure(\Closure $route, array $params)
    {
        ob_start();
        $value = call_user_func_array($route, [...$params, $this->app]);
        $content = ob_get_clean();

        $this->outputContent($value, $content);
    }

    /**
     * Outputs the content from an object
     * @param object $object The object to output
     */
    protected function outputFromObject(object $object)
    {
        if ($object instanceof ContentInterface) {
            $object->output();
        } else {
            $this->app->send($object);
        }
    }

    /**
     * Outputs the content from an array
     * @param array $array The array to output
     */
    protected function outputFromArray(array $array)
    {
        $this->app->send($array);
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
}
