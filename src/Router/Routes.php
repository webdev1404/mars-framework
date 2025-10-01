<?php
/**
* The Routes Class
* @package Mars
*/

namespace Mars\Router;

use Mars\App\Handlers;

/**
 * The Routes Class
 * Stores and handles the defined routes
 */
class Routes extends Base
{
    /**
     * @var array $list The defined routes list
     */
    public array $list = [];

    /**
     * @var array $supported_sources The list of supported route sources
     */
    public protected(set) array $supported_sources = [
        'pages' => \Mars\Router\Sources\Pages::class,
        'files' => \Mars\Router\Sources\Files::class,
    ];

    /**
     * @var Handlers $sources The sources object
     */
    public protected(set) Handlers $sources {
        get {
            if (isset($this->sources)) {
                return $this->sources;
            }

            $this->sources = new Handlers($this->supported_sources, null, $this->app);

            return $this->sources;
        }
    }

    /**
     * Loads the routes from all the sources
     * @return array The list of routes
     */
    public function load() : array
    {
        foreach ($this->sources as $source) {
            $source->routes = $this;
            $source->load();
        }

        return $this->list;
    }

    /**
     * Resets the routes list
     * @return static
     */
    public function reset() : static
    {
        $this->list = [];

        return $this;
    }
}
