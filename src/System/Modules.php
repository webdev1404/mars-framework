<?php
/**
* The System's Modules Class
* @package Mars
*/

namespace Mars\System;

use Mars\Extensions\Module;
use Mars\Extensions\Modules as BaseModules;

/**
 * The System's Modules Class
 */
class Modules extends BaseModules
{
    /**
     * @var array $instances The instances of the loaded modules
     */
    protected array $instances = [];

    /**
     * Returns a new module instance
     * @param string $name The name of the module
     * @param array $params Optional parameters to pass to the module constructor
     * @return Module|null The module or null if not found
     */
    public function get(string $name, array $params = [], bool $can_cache = true) : ?Module
    {
        if (!$this->isEnabled($name)) {
            return null;
        }

        if (!$can_cache) {
            return new static::$instance_class($name, $params, $this->app);
        }

        $key = $name;
        if ($params) {
            $key .= ':' . md5(serialize($params));
        }
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        $this->instances[$key] = new static::$instance_class($name, $params, $this->app);

        return $this->instances[$key];
    }

    /**
     * Boots the enabled modules
     */
    public function boot()
    {
        $list = $this->getFileList('boot.php', 'boot-list');
        
        foreach ($list as $name) {
            $module = $this->get($name, can_cache: false);
            $module->boot();
        }
    }

    /**
     * Prepares the modules with a prepare.php file in their root folder
     */
    public function prepare()
    {
        $list = $this->getFileList('prepare.php', 'prepare-list');
        foreach ($list as $name) {
            $module = $this->get($name, can_cache: false);
            $module->prepare();
        }
    }

    /**
     * Returns the list of modules which have a certain file in their root folder
     * @param string $file The file to look for
     * @param string $cache_file The cache file to use
     * @return array The list of modules which have the file in their root folder
     */
    protected function getFileList(string $file, string $cache_file): array
    {
        $list = $this->cache->get($cache_file);
        if ($this->app->development) {
            $list = null;
        }

        if ($list !== null) {
            return $list;
        }

        $list = [];
        $modules = $this->getEnabled();

        foreach ($modules as $name => $module) {
            $filename = $module . '/' . $file;
            if (is_file($filename)) {
                $list[] = $name;
            }
        }

        $this->cache->set($cache_file, $list);

        return $list;
    }
}
