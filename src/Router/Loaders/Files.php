<?php
/**
* The Files Loader Class
* @package Mars
*/

namespace Mars\Router\Loaders;

use Mars\Extensions\Modules\Module;
use Mars\Extensions\Modules\Modules;

/**
 * The Files Loader Class
 * Loads the routes from files
 */
class Files extends Loader
{
    /**
     * @var string $current_filename The current filename being loaded
     */
    protected string $current_filename = '';

    /**
     * @see Loader::load()
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
        foreach ($modules->getEnabled() as $module_path) {
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
     * @param string|callable|array $actions The action(s). Can be a closure or a string (class name). If array will register multiple actions, one for each language
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string $name The name of the route
     * @return static
     */
    public function all(string $route, string|callable|array $actions, string|array|null $languages = null, string $name = '') : static
    {
        $data = ['filename' => $this->current_filename];

        $this->add($route, $actions, static::ALLOWED_METHODS, $languages, 'action', $data, $name);

        return $this;
    }

    /**
     * Handles a GET request
     * @param string|array $routes The route(s)
     * @param string|callable|array $actions The action(s). Can be a closure or a string (class name). If array will register multiple actions, one for each language
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string $name The name of the route
     * @return static
     */
    public function get(string|array $routes, string|callable|array $actions, string|array|null $languages = null, string $name = '') : static
    {
        $data = ['filename' => $this->current_filename];
        
        return $this->add($routes, $actions, 'get', $languages, 'action', $data, $name);
    }

    /**
     * Handles a POST request
     * @param string|array $routes The route(s)
     * @param string|callable|array $actions The action(s). Can be a closure or a string (class name). If array will register multiple actions, one for each language
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     *
     * @return static
     */
    public function post(string|array $routes, string|callable|array $actions, string|array|null $languages = null) : static
    {
        $data = ['filename' => $this->current_filename];
        
        return $this->add($routes, $actions, 'post', $languages, 'action', $data);
    }

    /**
     * Handles a PUT request
     * @param string|array $routes The route(s)
     * @param string|callable|array $actions The action(s). Can be a closure or a string (class name). If array will register multiple actions, one for each language
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function put(string|array $routes, string|callable|array $actions, string|array|null $languages = null) : static
    {
        $data = ['filename' => $this->current_filename];
        
        return $this->add($routes, $actions, 'put', $languages, 'action', $data);
    }

    /**
     * Handles a DELETE request
     * @param string|array $routes The route(s)
     * @param string|callable|array $actions The action(s). Can be a closure or a string (class name). If array will register multiple actions, one for each language
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @return static
     */
    public function delete(string|array $routes, string|callable|array $actions, string|array|null $languages = null) : static
    {
        $data = ['filename' => $this->current_filename];

        return $this->add($routes, $actions, 'delete', $languages, 'action', $data);
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
        $data = ['filename' => $this->current_filename];

        foreach ($routes as $route => $action) {
            $this->add($route, [$language => $action], $methods, $language, 'action', $data);
        }

        return $this;
    }

    /**
     * Handles a module request
     * @param string|array $routes The route(s)
     * @param string $module The name of the module
     * @param string|array $action The action to execute. If array, the request method assigned to each action can be specified. Eg: ['get' => 'form', 'post' => 'register']
     * @param array $params The params to pass to the module, if any
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $methods The request methods to handle. Default: GET, POST
     * @param string $name The name of the route
     * @return static
     */
    public function module(string|array $routes, string $module, string|array $action, array $params = [], string|array|null $languages = '*', array $methods = ['get', 'post'], string $name = '') : static
    {
        $data = ['name' => $module, 'action' => $action, 'params' => $params];

        return $this->add($routes, null, $methods, $languages, 'module', $data, $name);
    }
    
    /**
     * Handles a template request
     * @param string|array $routes The route(s)
     * @param string $template The template's name
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $methods The request methods to handle. Default: get
     * @param string $name The name of the route
     * @return static
     */
    public function template(string|array $routes, string $template, string|array|null $languages = '*', array $methods = ['get'], string $name = '') : static
    {
        $data = ['template' => $template];

        return $this->add($routes, null, $methods, $languages, 'template', $data, $name);
    }

    /**
     * Handles a page request
     * @param string|array $routes The route(s)
     * @param string $page The page's name
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param array $methods The request methods to handle. Default: GET
     * @param string $name The name of the route
     * @return static
     */
    public function page(string|array $routes, string $page, string|array|null $languages = null, array $methods = ['get'], string $name = '') : static
    {
        $data = ['page' => $page];

        return $this->add($routes, null, $methods, $languages, 'page', $data, $name);
    }

    /**
     * Removes a route
     * @param string|array $routes The route(s) to remove
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string|array $methods The request methods to remove. Default: all methods
     * @return static
     */
    public function remove(string|array $routes, string|array|null $languages = '*', string|array $methods = []) : static
    {
        $routes_list = $this->app->array->get($routes);
        $methods = $this->getMethods($methods);

        foreach ($routes_list as $route_lang => $route) {
            $languages = $this->getLanguages($route_lang, $routes, null, $languages);
            if (!$languages) {
                continue;
            }

            $route = $this->getName($route);
            $hash = $this->getHash($route);
            $prefix = $this->getPrefix($route);

            foreach ($methods as $method) {
                foreach ($languages as $language) {
                    $this->unloadHash($method, $language, $route, $prefix, $hash);
                }
            }
        }

        return $this;
    }

    /**
     * Adds a route
     * @param string|array $route The route(s)
     * @param string|callable|array|null $actions The action(s). Can be a closure or a string (class name). If array will register multiple actions, one for each language
     * @param string $method The method: GET/POST/PUT/DELETE
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string $type The route type
     * @param array $data Route's data
     * @param string $name The name of the route
     * @return static
     */
    protected function add(string|array $routes, string|callable|array|null $actions, string|array $method, string|array|null $languages, string $type, array $data, string $name = '') : static
    {
        $this->addHashes($routes, $actions, $languages, $method, $type, $data, $name);

        return $this;
    }

    /**
     * Adds the hashes for a route
     * @param string|array $route The route(s). If array will register multiple routes, one for each language
     * @param string|callable|array|null $actions The action(s). Can be a closure or a string (class name). If array will register multiple actions, one for each language
     * @param string|array|null $languages The language to use for the route, if any. If null, will use the default language. If '*', will use all languages
     * @param string|array $methods The methods: GET/POST/PUT/DELETE
     * @param string $type The route type
     * @param array $data Route's data
     */
    protected function addHashes(string|array $routes, string|callable|array|null $actions, string|array|null $languages, string|array $methods, string $type, array $data = [], string $name = '') : void
    {
        $hashes = [];
        $routes_list = $this->app->array->get($routes);
        $methods = $this->getMethods($methods);

        foreach ($routes_list as $route_lang => $route) {
            $languages = $this->getLanguages($route_lang, $routes, $actions, $languages);
            if (!$languages) {
                continue;
            }

            $route = $this->getName($route);
            $prefix = $this->getPrefix($route);
            $hash = $this->getHash($route);

            foreach ($methods as $method) {
                if (!in_array($method, static::ALLOWED_METHODS)) {
                    throw new \Exception('Invalid route method: ' . $method);
                }

                foreach ($languages as $language) {
                    $action = null;
                    if ($actions !== null) {
                        //check if we have an action for this language
                        $action = $this->getAction($language, $actions);
                        if (!$action) {
                            continue;
                        }
                    }

                    //store the name of the route for GET requests only
                    if ($name && $method == 'get') {
                        $this->loadName($language, $name, $route);
                    }

                    $this->loadHash($method, $language, $route, $prefix, $hash, $type, $data, $action);
                }
            }
        }
    }

    /**
     * Returns the action for a specific language
     * @param string $language The language code
     * @param string|callable|array $action The action
     * @return string|callable|null The action for the language, or null if not found
     */
    protected function getAction(string $language, string|callable|array $action) : string|callable|null
    {
        if (!is_array($action)) {
            return $action;
        }
        if (isset($action[$language])) {
            return $action[$language];
        }
        if (isset($action['*'])) {
            return $action['*'];
        }

        return null;
    }

    /**
     * Returns the languages to use for a route
     * @param string $route_lang The route's language
     * @param string|array $routes The route(s)
     * @param string|array|null $languages The languages to use
     * @return array The list of languages
     */
    protected function getLanguages(string $route_lang, string|array $routes, string|callable|array|null $action, string|array|null $languages = null) : array
    {
        if (is_array($routes) && is_array($action)) {
            return $this->getLanguagesForRoute($route_lang, $routes, $action);
        } elseif (is_array($routes)) {
            return $this->getLanguagesForRoute($route_lang, $routes);
        } elseif (is_array($action)) {
            return $this->getLanguagesForAction($action);
        }

        return $this->getLanguagesList($languages);
    }

    /**
     * Returns the languages to use for this route
     * @param string $route_lang The route's language
     * @param array $routes The route(s)
     * @param array|null $action The action(s)
     * @return array The list of languages
     */
    protected function getLanguagesForRoute(string $route_lang, array $routes, ?array $action = null) : array
    {
        $languages = [];

        if ($route_lang == '*') {
            $languages = $this->app->lang->codes;

            //remove the languages for which we have routes defined
            $languages = array_diff($languages, array_keys($routes));
        } else {
            $languages = isset($this->app->lang->codes_list[$route_lang]) ? [$route_lang] : [];
        }

        if ($action) {
            if (isset($action['*'])) {
                return $languages;
            } else {
                $languages = array_intersect($languages, array_keys($action));
            }
        }

        return $languages;
    }

    /**
     * Returns the languages to use for this action
     * @param array $action The action(s)
     * @return array The list of languages
     */
    protected function getLanguagesForAction(array $action) : array
    {
        if (isset($action['*'])) {
            return $this->app->lang->codes;
        }

        $languages = array_keys($action);

        return array_intersect($languages, $this->app->lang->codes);
    }

    /**
     * Returns the list of languages
     * @param string|array|null $languages The languages to use
     * @return array The list of languages
     */
    protected function getLanguagesList(string|array|null $languages = null) : array
    {
        if ($languages === null) {
            return [$this->app->lang->default_code];
        } elseif ($languages == '*') {
            return $this->app->lang->codes;
        }
        
        $languages = $this->app->array->get($languages);

        return array_intersect($languages, $this->app->lang->codes);
    }
}
