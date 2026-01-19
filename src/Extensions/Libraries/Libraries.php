<?php
/**
* The Libraries Class
* @package Mars
*/

namespace Mars\Extensions\Libraries;

use Mars\App\Kernel;
use Mars\Extensions\Extensions;

/**
 * The Libraries Class
 * Base class for all libraries
 */
abstract class Libraries extends Extensions
{
    use Kernel;

    /**
     * @var array $loaded Array listing the loaded libraries
     */
    protected array $loaded = [];

    /**
     * Determines if a library is loaded
     * @param string $name The name of the library
     * @return bool True if the library is loaded, false otherwise
     */
    public function isLoaded(string $name): bool
    {
        return isset($this->loaded[$name]);
    }

    /**
     * Loads a library
     * @param string $name The name of the library
     * @return static
     * @throws \Exception If the library does not exist
     */
    public function load(string $name) : static
    {
        if (isset($this->loaded[$name])) {
            return $this;
        }

        $library = $this->get($name);
        $library->boot();

        $this->loaded[$name] = true;

        return $this;
    }
}