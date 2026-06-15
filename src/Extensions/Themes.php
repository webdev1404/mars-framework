<?php
/**
* The Themes Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App\HiddenProperty;
use Mars\Cache\Cacheable;
use Mars\Extensions\Setup\Theme as ThemeSetup;

/**
 * The Themes Class
 */
class Themes extends Extensions
{
    /**
     * @internal
     */
    protected static array $supports = ['config', 'lang'];

    /**
     * @internal
     */
    protected static bool $list_use_all = true;

    /**
     * @internal
     */
    protected static ?array $list_all = null;

    /**
     * @internal
     */
    protected static string $instance_class = Theme::class;

    /**
     * @internal
     */
    protected static string $setup_class = ThemeSetup::class;

    /**
     * @internal
     */
    #[HiddenProperty]
    public Cacheable $cache {
        get => $this->app->cache->themes;
    }
}
