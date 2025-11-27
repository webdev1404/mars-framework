<?php
/**
* The Cacheable Driver Interface
* @package Mars
*/

namespace Mars\Cache\Cacheable;

/**
 * The Cacheable Driver Interface
 */
interface CacheableInterface
{
    /**
     * Returns the cached content
     * @param string $filename The filename
     * @param bool $unserialize Whether to unserialize the content after retrieving
     * @return mixed The content or null if not found
     */
    public function get(string $filename, bool $unserialize) : mixed;

    /**
     * Caches content
     * @param string $filename The filename
     * @param mixed $content The content to store
     * @param bool $serialize Whether to serialize the content before storing
     * @return bool True on success, false on failure
     */
    public function set(string $filename, mixed $content, bool $serialize) : bool;

    /**
     * Creates a new cache file
     * @param string $filename The name of the cache file
     */
    public function create(string $filename);

    /**
     * Checks if a cache file exists
     * @param string $filename The name of the cache file
     * @return bool True if the cache file exists, false otherwise
     */
    public function exists(string $filename) : bool;

    /**
     * Returns the timestamp when the asset was last modified
     * @param string $filename The filename
     * @return int The timestamp when the file was last modified
     */
    public function getLastModified(string $filename) : int;

    /**
     * Deletes an asset
     * @return bool True on success, false on failure
     */
    public function delete(string $filename) : bool;

    /**
     * Deletes all keys from the cache
     * @param string $dir The directory to clean
     */
    public function clean(string $dir);
}
