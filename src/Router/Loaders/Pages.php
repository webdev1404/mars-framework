<?php
/**
* The Pages Loader Class
* @package Mars
*/

namespace Mars\Router\Loaders;

use Mars\Extensions\Modules\Module;

/**
 * The Pages Loader Class
 * Loads the page routes
 */
class Pages extends Loader
{
    /**
     * @var string $homepage The name of the homepage file
     */
    protected string $homepage = 'homepage';

    /**
     * @var string $method The HTTP method for the page routes
     */
    protected string $method = 'get';

    /**
     * @see Loader::load()
     * {@inheritDoc}
     */
    public function load()
    {
        if (!$this->app->config->routes->pages_autoload) {
            return;
        }

        $paths = $this->getPaths();
        foreach ($paths as $path) {
            $files = $this->getFromPath($path);

            foreach ($files as $file) {
                $filename = $path . '/' . $file;
                $route = $this->getRoute($file);
                $prefix = $this->getPrefix($route);
                $name = 'page.' . $route;
                $languages = $this->getLanguages($file, $files);

                if (!$languages) {
                    continue;
                }

                foreach ($languages as $language) {
                    $hash = $this->getHash($route, $language, 'get');

                    $this->loadHash($this->method, $language, $route, $prefix, $hash, 'page', $name, ['page' => $filename], null);
                }
            }
        }
    }

    /**
     * Returns the list of paths from where to build page routes
     * @return array The list of paths
     */
    protected function getPaths() : array
    {
        $paths = [];
        foreach ($this->app->modules->getEnabled() as $module_path) {
            $module_path = $module_path . '/' . Module::DIRS['pages'];
            if (is_dir($module_path)) {
                $paths[] = $module_path;
            }
        }

        $paths[] = $this->app->app_path . '/pages';

        return $paths;
    }

    /**
     * Returns the list of pages from a path
     * @param string $path The path where to look for the pages
     * @return array The list of pages
     */
    protected function getFromPath(string $path) : array
    {
        if (!is_dir($path)) {
            return [];
        }

        return $this->app->dir->getFiles($path, true, false, extensions: ['php']);
    }

    /**
     * Returns the route name from a file name
     * @param string $file The file
     * @return string The route name
     */
    protected function getRoute(string $file) : string
    {
        $route = $this->app->file->getFullStem($file);
        if ($route == $this->homepage) {
            return '/';
        }

        $parts = explode('/', $route);
        $lang = $parts[0] ?? '';

        if (!$lang) {
            return $route;
        }

        if (!isset($this->app->lang->codes_list[$lang])) {
            return $route;
        }

        return implode('/', array_slice($parts, 1));
    }

    /**
     * Returns the language codes for a file name
     * @param string $file The file
     * @return array The language codes
     */
    protected function getLanguages(string $file, array $files) : array
    {
        $parts = explode('/', $file);
        $lang = $parts[0] ?? '';

        if (count($parts) >= 2 && isset($this->app->lang->codes_list[$lang])) {
            return [$lang];
        }

        if (!$this->app->lang->multi) {
            return $this->app->lang->codes;
        }

        $languages = [];
        foreach ($this->app->lang->codes as $language) {
            $lang_file = $language . '/' . $file;
            if (in_array($lang_file, $files)) {
                continue;
            }

            $languages[] = $language;
        }

        return $languages;
    }
}
