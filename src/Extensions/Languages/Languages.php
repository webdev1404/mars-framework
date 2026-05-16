<?php
/**
* The Languages Class
* @package Mars
*/

namespace Mars\Extensions\Languages;

use Mars\Cache\Cacheable;
use Mars\Extensions\Extensions;

/**
 * The Languages Class
 */
class Languages extends Extensions
{
    /**
     * @internal
     */
    protected static ?array $list_enabled = null;

    /**
     * @internal
     */
    protected static ?array $list_all = null;

    /**
     * @internal
     */
    protected static string $list_config_key = 'language.codes';

    /**
     * @internal
     */
    protected static string $instance_class = Language::class;

    /**
     * @internal
     */
    public Cacheable $cache {
        get => $this->app->cache->languages;
    }
}
