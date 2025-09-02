<?php
/**
* The Module Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\Extensions\Modules\Abilities\LanguagesTrait;
use Mars\Extensions\Modules\Abilities\TemplatesTrait;

/**
 * The Module Class
 * Base class for all module extensions
 */
class Module extends Extension
{
    use LanguagesTrait;
    use TemplatesTrait;

    /**
     * @const array DIR The locations of the used extensions subdirs
     */
    public const array DIRS = [
        'assets' => 'assets',
        'languages' => 'languages',
        'templates' => 'templates',
        'blocks' => 'blocks',
        'plugins' => 'plugins',
        'routes' => 'routes',
        'setup' => 'setup',
    ];

    /**
     * @internal
     */
    protected static ?array $list = null;

    /**
     * @internal
     */
    protected static string $list_config_file = 'modules.php';

    /**
     * @internal
     */
    protected static ?array $available_list = null;

    /**
     * @internal
     */
    protected static bool $list_filter = true;

    /**
     * @internal
     */
    protected static string $type = 'module';

    /**
     * @internal
     */
    protected static string $base_dir = 'modules';

    /**
     * @internal
     */
    protected static string $base_namespace = "\\Modules";

    /**
     * @internal
     */
    protected static string $setup_class = \Mars\Extensions\Setup\Module::class;

    /**
     * @var string $lang_key The key used to store the language strings
     */
    public protected(set) string $lang_key {
        get {
            if (isset($this->lang_key)) {
                return $this->lang_key;
            }

            $this->lang_key = 'module.' . $this->name;

            return $this->lang_key;
        }
    }

    /**
     * Returns the namespace for the specified plugin name
     * @param string $module_name The name of the module the plugin belongs to
     * @param string $plugin_name The name of the plugin
     * @return string The namespace for the plugin
     */
    public static function getPluginNamespace(string $module_name, string $plugin_name) : string
    {
        return static::$base_namespace . '\\' . App::getClass($module_name) . '\\' . App::getClass(static::DIRS['plugins']) . '\\' . App::getClass($plugin_name);
    }
}
