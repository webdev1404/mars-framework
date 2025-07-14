<?php
/**
* The Preload Urls Class
* @package Mars
*/

namespace Mars\Document\Links\Hints;

use Mars\App\Kernel;
use Mars\Data\ListTrait;

/**
 * The Preload Urls Class
 * Class containing the preload functionality used by a url
 */
class Preload
{
    use Kernel;
    use ListTrait;

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
     * @return static
     */
    public function load(string|array $urls) : static
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
