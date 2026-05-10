<?php
/**
* The Cacheable File Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

/**
 * The Cacheable File Driver
 * Driver which stores the cached data as serialized (igbinary or json) text
 */
class File extends Text
{
    /**
     * @var string $extension The extension to use for the cached files
     */
    protected string $extension = 'data';

    /**
     * @see CacheableInterface::get()
     * {@inheritDoc}
     */
    public function get(string $filename) : mixed
    {
        $content = parent::get($filename);
        if ($content === null) {
            return null;
        }

        return $this->app->serializer->unserializeData($content);
    }

    /**
     * @see CacheableInterface::set()
     * {@inheritDoc}
     */
    public function set(string $filename, mixed $content) : bool
    {
        $content = $this->app->serializer->serializeData($content);

        return parent::set($filename, $content);
    }
}