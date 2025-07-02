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
     * @see Extension::getError()
     * {@inheritdoc}
     */
    protected function getError() : string
    {
        return "Module '{$this->name}' not found or not enabled in config/modules.php";
    }

    /**
     * Returns the list of available extensions of this type
     * @return array The list of available extensions
     */
    public static function getList() : array
    {
        $app = static::getApp();

        $list = parent::getList();
        
        $enabled_list = $app->config->read('modules.php');
        
        //filter out the modules that are not enabled
        return array_filter($list, fn($module) => in_array($module, $enabled_list), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Returns the namespace for the specified plugin name
     * @param string $plugin_name The name of the plugin
     * @return string The namespace for the plugin
     */
    public function getPluginNamespace(string $plugin_name) : string
    {
        return $this->namespace . '\\' . App::getClass(static::DIRS['plugins']) . '\\' . App::getClass($plugin_name);
    }
}
