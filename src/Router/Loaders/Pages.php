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
     * {@inheritdoc}
     */
    public function load()
    {
        if (!$this->app->config->routes->pages_autoload) {
            return;
        }

        $dirs = $this->getDirsList();
        foreach ($dirs as $dir) {
            $files = $this->getFromDir($dir);

            foreach ($files as $file) {
                $filename = $dir . '/' . $file;
                $route = $this->getRoute($file);
                $prefix = $this->getPrefix($route);
                $languages = $this->getLanguages($file, $files);

                if (!$languages) {
                    continue;
                }

                foreach ($languages as $language) {
                    $hash = $this->getHash($route, $language, 'get');

                    $this->loadHash($this->method, $language, $route, $prefix, $hash, 'page', ['page' => $filename], null);
                }
            }
        }
    }

    /**
     * Returns the list of dirs from where to build page routes
     * @return array The list of dirs
     */
    protected function getDirsList() : array
    {
        $dirs = [];
        foreach ($this->app->modules->getEnabled() as $module_path) {
            $module_dir = $module_path . '/' . Module::DIRS['pages'];
            if (is_dir($module_dir)) {
                $dirs[] = $module_dir;
            }
        }

        $dirs[] = $this->app->app_path . '/pages';

        return $dirs;
    }

    /**
     * Returns the list of pages from a dir
     * @param string $dir The dir where to look for the pages
     * @return array The list of pages
     */
    protected function getFromDir(string $dir) : array
    {
        if (!is_dir($dir)) {
            return [];
        }

        return $this->app->dir->getFiles($dir, true, false, extensions: ['php']);
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
