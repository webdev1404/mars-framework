<?php
/**
* The Routes Loader Class
* @package Mars
*/

namespace Mars\Router;

use Mars\Mvc\Controller;

/**
 * The Routes Loader Class
 * Routes loading and handling class
 */
class Loader extends Routes
{
    /**
     * @var array $supported_sources The list of supported route sources
     */
    public protected(set) array $supported_sources = [
        'pages' => \Mars\Router\Sources\Loaders\Pages::class,
        'files' => \Mars\Router\Sources\Loaders\Files::class,
    ];

    /**
     * Returns the route by its hash
     * @param string $hash The hash of the route
     * @param array $data The route data
     * @return array|null The route, or null if not found
     */
    public function getByHash(string $hash, array $data) : array|null
    {
        $source = $this->sources->get($data['type']);
        $source->routes = $this;

        return $source->getByHash($hash, $data);
    }

    /**
     * Returns the route from a preg match
     * @param array $hashes The list of hashes
     * @return array|null The route, or null if not found
     */
    public function getByPreg(array $hashes) : array|null
    {
        foreach ($this->sources as $source) {
            $source->routes = $this;
            $route = $source->getByPreg($hashes);
            if ($route) {
                return $route;
            }
        }

        return null;
    }
}
