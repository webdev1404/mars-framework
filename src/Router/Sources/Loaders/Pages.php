<?php
/**
* The Pages Routes Loader Class
* @package Mars
*/

namespace Mars\Router\Sources\Loaders;

use Mars\Content\Page;

/**
 * The Files Routes Loader Class
 * Loads routes from pages
 */
class Pages extends \Mars\Router\Sources\Pages
{
    /**
     * Returns the route by its hash
     * @param string $hash The hash of the route
     * @param array $data The route data
     * @return array|null The route, or null if not found
     */
    public function getByHash(string $hash, array $data) : array|null
    {
        if (!$this->app->config->routes_pages_autoload) {
            return null;
        }

        if (!is_file($data['filename'])) {
            return null;
        }

        $action = fn () => new Page($data['filename'], $this->app);

        return [$action, []];
    }

    /**
     * @internal
     */
    public function getByPreg(array $hashes) : array|null
    {
        return null;
    }
}
