<?php
/**
* The Preload Urls Class
* @package Mars
*/

namespace Mars\Document\Urls;

use Mars\App\InstanceTrait;
use Mars\Lists\ListSimpleTrait;

/**
 * The Preload Urls Class
 * Class containing the preload functionality used by a url
 */
class Preload
{
    use InstanceTrait;
    use ListSimpleTrait;

    /**
     * @var array $urls Array with all the urls to preload
     */
    protected array $urls = [];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /**
     * Loads the urls to be preloaded
     * @param string|array $urls The urls to preload
     * @param string $type The type of the preload
     * @return static
     */
    public function load(string|array $urls, string $type = '') : static
    {
        return $this->add($urls);
    }

    /**
     * Unloads the preloaded urls
     * @param string|array $urls The urls to unload
     * @return static
     */
    public function unload(string|array $urls) : static
    {
        return $this->remove($urls);
    }
}