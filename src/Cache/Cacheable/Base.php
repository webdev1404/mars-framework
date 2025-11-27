<?php
/**
* The Cachable File Driver
* @package Mars
*/

namespace Mars\Cache\Cacheable;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Cachable File Driver
 * Driver which stores on disk the cached resources
 */
abstract class Base
{
    use Kernel;

    /**
     * @var bool $files_cache_use Whether to use existing files or not in file based drivers
     */
    protected bool $files_cache_use = false;

    /**
     * @var string $type The type of keys used to store the cached resources
     */
    protected string $type = '';

    /**
     * Constructor
     * @param bool $files_cache_use_use Whether to use existing files or not
     * @param string $type The type of keys used to store the cached resources
     * @param App $app The app object
     */
    public function __construct(bool $files_cache_use, string $type, App $app)
    {
        $this->app = $app;
        $this->files_cache_use = $files_cache_use;
        $this->type = $type;
    }
}
