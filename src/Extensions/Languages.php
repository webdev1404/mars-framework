<?php
/**
* The Languages Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App\HiddenProperty;
use Mars\Cache\Cacheable;
use Mars\Extensions\Setup\Language as LanguageSetup;

/**
 * The Languages Class
 */
class Languages extends Extensions
{
    /**
     * @internal
     */
    protected static array $supports = ['config', 'routes'];

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
    protected static string $setup_class = LanguageSetup::class;
    
    /**
     * @internal
     */
    #[HiddenProperty]
    public Cacheable $cache {
        get => $this->app->cache->languages;
    }
}
