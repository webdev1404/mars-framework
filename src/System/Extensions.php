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
class Extensions
{
    use Kernel;

    /**
     * @var array $types The defined supported extension types and their getters
     */
    public protected(set) array $types {
        get {
            if (isset($this->types)) {
                return $this->types;
            }

            $this->types = [
                'module' => fn () => $this->app->modules,
                'theme' => fn () => $this->app->theme->manager,
                'language' => fn () => $this->app->lang->manager,
            ];

            return $this->types;
        }
    }

    /**
     * @var array $list The list of all enabled extensions and their types
     */
    public array $list {
        get {
            if (isset($this->list)) {
                return $this->list;
            }

            $this->list = [];

            foreach ($this->types as $name => $getter) {
                $manager = $getter();

                $list = array_map(fn ($path) => $name, $manager->getEnabled());

                $this->list = array_merge($this->list, $list);
            }

            return $this->list;
        }
    }

    /**
     * Gets the list of supported extensions and their getters
     * @return array The extensions
     */
    public function getTypes() : array
    {
        return $this->types;
    }

    /**
     * Gets a specific manager by type
     * @param string $type The type of the manager
     * @return BaseExtensions|null The manager instance or null if not found
     */
    public function getManager(string $type) : ?BaseExtensions
    {
        if (isset($this->types[$type])) {
            return $this->types[$type]();
        }

        return null;
    }

    /**
     * Returns the type of a given extension name
     * @param string $name The name of the extension
     * @return string|null The type of the extension or null if not found
     */
    public function getType(string $name) : ?string
    {
        return $this->list[$name] ?? null;
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
     * @return Extension|null The extension instance or null if not found
     */
    public function get(string $name) : ?Extension
    {
        if (!isset($this->list[$name])) {
            return null;
        }

        $type = $this->list[$name];

        return $this->getManager($type)->get($name);
    }
}
