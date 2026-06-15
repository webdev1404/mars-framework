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
    public array $enabled {
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

    public protected(set) array $enabled_supports = [];

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
     * Returns the type of a given extension name
     * @param string $name The name of the extension
     * @param string $support The required support. Eg: 'config'
     * @return string|null The type of the extension or null if not found
     */
    public function getType(string $name, string $support = '') : ?string
    {
        if ($support) {
            $this->enabled_supports[$support] ??= $this->getEnabledWithSupport($support);

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
    public function get(string $name, string $support = '') : ?Extension
    {
        if ($support) {
            $this->enabled_supports[$support] ??= $this->getEnabledWithSupport($support);

            if (!isset($this->enabled_supports[$support][$name])) {
                return null;
            }

            $type = $this->enabled_supports[$support][$name];
        } else {
            if (!isset($this->enabled[$name])) {
                return null;
            }

            $type = $this->enabled[$name];
        }

        return $this->getManager($type)->get($name);
    }

    /**
     * Gets the list of enabled extensions that support a specific feature
     * @param string $support The required support. Eg: 'config'
     * @return array The list of enabled extensions
     */
    protected function getEnabledWithSupport(string $support) : array
    {
        $enabled = [];

        foreach ($this->list as $type => $manager) {
            if (!$manager->supports($support)) {
                continue;
            }

            $enabled_list = array_map(fn ($path) => $type, $manager->getEnabled());

            $enabled = array_merge($enabled, $enabled_list);
        }

        return $enabled;
    }

    /**
     * @internal
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->list);
    }
}
