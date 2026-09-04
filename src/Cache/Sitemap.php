<?php
/**
* The Storage Cache Class
* @package Mars
*/

namespace Mars\Cache;

/**
 * The Storage Cache Class
 * Class which handles the caching of user/app storage data
 */
class Sitemap extends Cache
{
    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'sitemap';

    /**
     * Gets the filename for a cache file
     * @param string $name The name of the file
     * @return string The filename
     */
    public function getFilename(string $name) : string
    {
        return $this->path . '/' . $name;
    }
}

