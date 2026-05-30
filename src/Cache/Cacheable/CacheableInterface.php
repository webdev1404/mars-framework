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
     * @return mixed The content or null if not found
     */
    public function get(string $filename) : mixed;

    /**
     * Caches content
     * @param string $filename The filename
     * @param mixed $content The content to store
     * @return bool True on success, false on failure
     */
    public function set(string $filename, mixed $content) : bool;

    /**
     * Creates a new cache file
     * @param string $filename The name of the cache file
     * @return bool True on success, false on failure
     */
    public function create(string $filename) : bool;

    /**
     * Checks if a cache file exists
     * @param string $filename The name of the cache file
     * @return bool True if the cache file exists, false otherwise
     */
    public function has(string $filename) : bool;

    /**
     * Returns the timestamp when the asset was last modified
     * @param string $filename The filename
     * @return int The timestamp when the file was last modified
     */
    public function getLastModified(string $filename) : int;

    /**
     * Deletes a cached file
     * @param string $filename The name of the cache file to delete
     * @return bool True on success, false on failure
     */
    public function delete(string $filename) : bool;

    /**
     * Deletes all keys from the cache
     * @param string $path The directory to clean
     * @param int|null $expire_hours The number of hours after which cached items expire. If null, will not consider expiration
     */
    public function clean(string $path, ?int $expire_hours = null);
}
