<?php
/**
* The Files Routes Source Class
* @package Mars
*/

namespace Mars\Router\Sources;

use Mars\Content\Page;
use Mars\Content\Template;
use Mars\Extensions\Modules\Module;
use Mars\Extensions\Modules\Modules;
use Mars\Extensions\Modules\Components\Block;

/**
 * The Files Routes Source Class
 * Stores and handles the defined routes
 */
class Files extends Source
{
    /**
     * @var string $current_filename The current filename being loaded
     */
    protected string $current_filename = '';

    /**
     * @see Source::load()
     * {@inheritdoc}
     */
    public function load()
    {
        $dirs = $this->getDirsList();
        foreach ($dirs as $dir) {
            $files_array = $this->getFromDir($dir);

            foreach ($files_array as $file) {
                $this->loadFile($file);
            }
        }
    }

    /**
     * Returns the list of dirs from where to build page routes
     * @return array The list of of dirs
     */
    protected function getDirsList() : array
    {
        $dirs = [];

        $modules = new Modules($this->app);
        foreach ($modules->get() as $module_path) {
            $module_dir = $module_path . '/' . Module::DIRS['routes'];
            if (is_dir($module_dir)) {
                $dirs[] = $module_dir;
            }
        }

        $dirs[] = $this->app->app_path . '/routes';

        return $dirs;
    }

    /**
     * Returns the list of cached route files
     * @param string $dir The dir where to look for cached route files
     * @return array The list of cached route files
     */
    protected function getFromDir(string $dir) : array
    {
        if (!is_dir($dir)) {
            return [];
        }

        return $this->app->dir->getFilesSorted($dir, true, true, extensions: ['php']);
    }

    /**
     * Loads the routes from a file
     * @param string $filename The file to load the routes from
     */
    public function loadFile(string $filename) : static
    {
        $app = $this->app;
        $router = $this;

        $this->current_filename = $filename;
        require($filename);

        return $this;
    }

    /**
     * Handles all request methods: GET, POST, PUT, DELETE
     * @param string $route The route
     * @param string|callable $action The action. Can be a closure or a string (class name)
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function all(string $route, string|callable $action, string|array|null $languages = null) : static
    {
        $this->add($route, $action, static::ALLOWED_METHODS, $languages);

        return $this;
    }

    /**
     * Handles a GET request
     * @param string $route The route
     * @param string|callable $action The action. Can be a closure or a string (class name)
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function get(string $route, string|callable $action, string|array|null $languages = null) : static
    {
        return $this->add($route, $action, 'get', $languages);
    }

    /**
     * Handles a POST request
     * @param string $route The route
     * @param string|callable $action The action. Can be a closure or a string (class name)
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function post(string $route, string|callable $action, string|array|null $languages = null) : static
    {
        return $this->add($route, $action, 'post', $languages);
    }

    /**
     * Handles a PUT request
     * @param string $route The route
     * @param string|callable $action The action. Can be a closure or a string (class name)
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function put(string $route, string|callable $action, string|array|null $languages = null) : static
    {
        return $this->add($route, $action, 'put', $languages);
    }

    /**
     * Handles a DELETE request
     * @param string $route The route
     * @param string|callable $action The action. Can be a closure or a string (class name)
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function delete(string $route, string|callable $action, string|array|null $languages = null) : static
    {
        return $this->add($route, $action, 'delete', $languages);
    }

    /**
     * Handles multiple routes for a specific language
     * @param string $language The language code
     * @param array $routes The routes to handle
     * @param array $methods The request methods to handle. Default: GET
     * @return static
     */
    public function lang(string $language, array $routes, array $methods = ['get']) : static
    {
        foreach ($routes as $route => $action) {
            $this->add($route, [$language => $action], $methods, $language);
        }

        return $this;
    }

    /**
     * Creates CRUD routes
     * @param string $route The base route
     * @param string|array $actions The actions. Can be a closure or a string (class name), or an array with 4 callable elements: create, read, update, delete
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $names The names of the actions. Default: create, read, update, delete
     * @return static
     */
    public function crud(string $route, string|array $actions, string|array|null $languages = null, array $names = ['create', 'read', 'update', 'delete' ]) : static
    {
        $actions_list = [];
        if (is_array($actions)) {
            if (count($actions) != 5) {
                throw new \Exception('Invalid CRUD route actions array. Must contain 5 elements: all, create, read, update, delete');
            }

            $actions_list = [
                'create' => $actions['create'] ?? $actions[0],
                'read' => $actions['read'] ?? $actions[1],
                'update' => $actions['update'] ?? $actions[2],
                'delete' => $actions['delete'] ?? $actions[3]
            ];
        } else {
            $actions_list = [
                'create' => $actions . '@create',
                'read' => $actions . '@read',
                'update' => $actions . '@update',
                'delete' => $actions . '@delete'
            ];
        }

        $route = rtrim($route, '/');
        $this->post($route . '/' . $names[0], $actions_list['create'], $languages);
        $this->get($route . '/' . $names[1] . '/{id}', $actions_list['read'], $languages);
        $this->post($route . '/' . $names[2] . '/{id}', $actions_list['update'], $languages);
        $this->post($route . '/' . $names[3] . '/{id}', $actions_list['delete'], $languages);

        return $this;
    }

    /**
     * Handles a block request
     * @param string $route The route
     * @param string $module_name The module the block belongs to
     * @param string $name The block's name
     * @param array $params The params to pass to the block, if any
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $methods The request methods to handle. Default: GET, POST
     * @return static
     */
    public function block(string $route, string $module_name, string $name = '', array $params = [], string|array|null $languages = '*', array $methods = ['get', 'post']) : static
    {
        $action = fn () => new Block($module_name, $name, $params, $this->app);

        return $this->add($route, $action, $methods, $languages);
    }
    
    /**
     * Handles a template request
     * @param string $route The route to handle
     * @param string $template The template's name
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $methods The request methods to handle. Default: get
     * @return static
     */
    public function template(string $route, string $template, string|array|null $languages = '*', array $methods = ['get']) : static
    {
        $action = fn () => new Template($template, $this->app);

        return $this->add($route, $action, $methods, $languages);
    }

    /**
     * Handles a page request
     * @param string $route The route to handle
     * @param string $page The page's name
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $methods The request methods to handle. Default: GET
     * @return static
     */
    public function page(string $route, string $page, string|array|null $languages = null, array $methods = ['get']) : static
    {
        $action = fn () => new Page($page, $this->app);

        return $this->add($route, $action, $methods, $languages);
    }

    /**
     * Removes a route
     * @param string|array $route The route to remove. Multiple routes can be passed as an array
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string|array $methods The request methods to remove. Default: all methods
     * @return static
     */
    public function remove(string|array $route, string|array|null $languages = '*', string|array $methods = []) : static
    {
        $routes = $this->app->array->get($route);
        $languages = $this->getLanguages($languages);
        $methods = $this->getMethods($methods);

        foreach ($routes as $route) {
            $route = $this->getName($route);

            foreach ($methods as $method) {
                foreach ($languages as $language) {
                    $hash = $this->getHash($route, $language, $method);

                    if (isset($this->routes->list[$hash])) {
                        unset($this->routes->list[$hash]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Adds a route
     * @param string $route The route
     * @param string|callable|array $action The action. Can be a closure or a string (class name). If array will register multiple routes
     * @param string $method The method: GET/POST/PUT/DELETE
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    protected function add(string $route, string|callable|array $action, string|array $method, string|array|null $languages = null) : static
    {
        $this->addHashes($route, $action, $languages, $method);

        return $this;
    }

    /**
     * Adds the hashes for a route
     * @param string $route The route
     * @param string|callable|array $action The action
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string $method The method: GET/POST/PUT/DELETE
     */
    protected function addHashes(string $route, string|callable|array $action, string|array|null $languages, string|array $method)
    {
        $hashes = [];
        $route = $this->getName($route);
        $languages = $this->getLanguages($languages);
        $methods = $this->getMethods($method);

        foreach ($methods as $method) {
            if (!in_array($method, static::ALLOWED_METHODS)) {
                throw new \Exception('Invalid route method: ' . $method);
            }

            foreach ($languages as $language) {
                $action_list = is_array($action) ? $action : [$language => $action];

                foreach ($action_list as $lang => $act) {
                    $hash = $this->getHash($route, $lang, $method);

                    if (!$this->canLoadHash($hash)) {
                        continue;
                    }

                    $this->routes->list[$hash] = $this->getData($route, $act);
                }
            }
        }
    }

    /**
     * Checks if a hash can be loaded
     * @param string $hash The hash to check
     * @return bool True if the hash can be loaded, false otherwise
     */
    protected function canLoadHash(string $hash) : bool
    {
        return true;
    }

    /**
     * Returns the data for a route
     * @param string $route The route
     * @param string|callable $action The action
     * @return array The route data
     */
    protected function getData(string $route, string|callable $action) : array
    {
        return ['type' => 'files', 'prefix' => $this->getPrefix($route), 'route' => $route, 'filename' => $this->current_filename, 'preg' => $this->getContainsPreg($route)];
    }
}
