<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\App\HiddenProperty;
use Mars\Extensions\Abilities\ConfigTrait;
use Mars\Extensions\Abilities\LanguagesTrait;

/**
 * The Plugin Class
 * Object corresponding to a plugin extension
 */
class Plugin extends Extension
{
    use ConfigTrait;
    use LanguagesTrait;
    
    /**
     * @const array DIRS The locations of the used extensions subdirs
     */
    public const array DIRS = [
        ...parent::DIRS,
        'bin' => 'bin',
    ];

    /**
     * @const array CACHE_DIRS The dirs to be cached
     */
    public const array CACHE_DIRS = ['languages'];

    /**
     * @var string $title The plugin's title
     */
    public protected(set) string $title = '';

    /**
     * @var array $hooks Array listing the defined hooks in the format [hook_name => method]
     */
    protected array $hooks = [];

    /**
     * @internal
     */
    #[HiddenProperty]
    public ?Extensions $manager {
        get => $this->app->plugins;
    }

    /**
     * @internal
     */
    protected static string $type = 'plugin';

    /**
     * @internal
     */
    protected static string $base_dir = 'plugins';

    /**
     * @internal
     */
    protected static string $base_namespace = "\\Plugins";

    /**
     * Builds the extension
     * @param string $name The name of the plugin
     * @param string $file The file of the plugin
     * @param array $params The params passed to the plugin, if any
     * @param App $app The app object
     */
    public function __construct(string $name, array $params = [], ?App $app = null)
    {
        parent::__construct($name, $params, $app);

        $this->app->plugins->addHooks($this, $this->hooks);
    }
}
