<?php
/**
* The Pages Routes Source Class
* @package Mars
*/

namespace Mars\Router\Sources;

use Mars\App\Handlers;
use Mars\Mvc\Controller;
use Mars\Content\Page;
use Mars\Content\Template;
use Mars\Extensions\Modules\Module;
use Mars\Extensions\Modules\Modules;

/**
 * The Pages Routes Source Class
 * Stores and handles the page routes
 */
class Pages extends Source
{
    /**
     * @var string $homepage The name of the homepage file
     */
    protected string $homepage = 'homepage';

    /**
     * @see Source::load()
     * {@inheritdoc}
     */
    public function load()
    {
        if (!$this->app->config->routes_pages_autoload) {
            return;
        }

        $dirs = $this->getDirsList();
        foreach ($dirs as $dir) {
            $files_array = $this->getFromDir($dir);

            foreach ($files_array as $file) {
                $filename = $dir . '/' . $file;
                $route = $this->getRoute($file);
                $lang = $this->getLanguage($file);
                $hash = $this->getHash($route, $lang, 'get');

                $this->routes->list[$hash] = $this->getData($route, $filename);
            }
        }
    }

    /**
     * Returns the data for a route
     * @param string $route The route
     * @param string $filename The filename
     * @return array The route data
     */
    protected function getData(string $route, string $filename) : array
    {
        return ['type' => 'pages', 'prefix' => $this->getPrefix($route),'filename' => $filename];
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
            $module_dir = $module_path . '/' . Module::DIRS['pages'];
            if (is_dir($module_dir)) {
                $dirs[] = $module_dir;
            }
        }

        $dirs[] = $this->app->app_path . '/pages';

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

        return $this->app->dir->getFilesSorted($dir, true, false, extensions: ['php']);
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

        if (!isset($this->app->lang->multi_list[$lang])) {
            return $route;
        }

        return implode('/', array_slice($parts, 1));
    }

    /**
     * Returns the language code from a file name
     * @param string $file The file
     * @return string The language code
     */
    protected function getLanguage(string $file) : string
    {
        $parts = explode('/', $file);
        $lang = $parts[0] ?? '';

        if (!$lang) {
            return $this->app->lang->default_code;
        }

        if (!isset($this->app->lang->multi_list[$lang])) {
            return $this->app->lang->default_code;
        }

        return $lang;
    }
}
