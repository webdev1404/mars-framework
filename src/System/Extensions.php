<?php
/**
* The Extensions Class
* @package Mars
*/

namespace Mars\System;

use Mars\App\Kernel;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions as BaseExtensions;

/**
 * The Extensions Class
 * Provides info about the System's Extensions
 */
class Extensions implements \IteratorAggregate
{
    use Kernel;

    /**
     * @var array $list The defined supported extension types and their managers
     */
    public protected(set) array $list {
        get {
            if (isset($this->list)) {
                return $this->list;
            }

            $this->list = [
                'plugin' => $this->app->plugins,
                'module' =>  $this->app->modules,
                'theme' => $this->app->theme->manager,
                'language' => $this->app->lang->manager,
            ];

            return $this->list;
        }
    }

    /**
     * @var array $enabled The list of all enabled extensions and their types
     */
    public protected(set) array $enabled {
        get {
            if (isset($this->enabled)) {
                return $this->enabled;
            }

            $this->enabled = [];

            foreach ($this->list as $type => $manager) {
                $enabled = array_map(fn ($path) => $type, $manager->getEnabled());

                $this->enabled = array_merge($this->enabled, $enabled);
            }

            return $this->enabled;
        }
    }

    /**
     * @var array $enabled_supports The list of all enabled extensions that support a specific feature
     */
    protected array $enabled_supports = [];

    /**
     * Gets a specific manager by type
     * @param string $type The type of the manager
     * @return BaseExtensions|null The manager instance or null if not found
     */
    public function getManager(string $type) : ?BaseExtensions
    {
        return $this->list[$type] ?? null;
    }

    /**
     * Gets all managers that support a specific feature
     * @param string $support The required support. Eg: 'config'
     * @return BaseExtensions[] The list of manager instances that support the feature
     */
    public function getManagers(string $support) : array
    {
        $managers = [];
        foreach ($this->list as $type => $manager) {
            if ($manager->supports($support)) {
                $managers[] = $manager;
            }
        }

        return $managers;
    }

    /**
     * Returns the type of a given extension name
     * @param string $name The name of the extension
     * @param string $support The required support. Eg: 'config'
     * @return string|null The type of the extension or null if not found
     */
    public function getType(string $name, string $support = '') : ?string
    {
        if ($support) {
            if (!isset($this->enabled_supports[$support])) {
                $this->enabled_supports[$support] = [];

                foreach ($this->list as $type => $manager) {
                    if (!$manager->supports($support)) {
                        continue;
                    }

                    $enabled_list = array_map(fn ($path) => $type, $manager->getEnabled());

                    $this->enabled_supports[$support] = array_merge($this->enabled_supports[$support], $enabled_list);
                }
            }

            return $this->enabled_supports[$support][$name] ?? null;
        } else {
            return $this->enabled[$name] ?? null;
        }
    }

    /**
     * Returns the path of a given extension name
     * @param string $name The name of the extension
     * @return string|null The path of the extension or null if not found
     */
    public function getPath(string $name, string $type = '') : ?string
    {
        if (!$type) {
            $type = $this->getType($name);
            if (!$type) {
                return null;
            }
        }

        $manager = $this->getManager($type);
        if (!$manager) {
            return null;
        }

        return $manager->getPath($name);
    }

    /**
     * Gets an extension by name
     * @param string $name The name of the extension
     * @param string $support The required support. Eg: 'config'
     * @return Extension|null The extension instance or null if not found
     */
    public function get(string $name, string $type = '', string $support = '') : ?Extension
    {
        if (!isset($this->enabled[$name])) {
            return null;
        }

        if (!$type) {
            $type = $this->getType($name, $support);
            if (!$type) {
                return null;
            }

            $manager = $this->getManager($type);
        } else {
            $manager = $this->getManager($type);
            if (!$manager || !$manager->supports($support)) {
                return null;
            }
        }

        return $manager->get($name);
    }

    /**
     * @internal
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->list);
    }
}
