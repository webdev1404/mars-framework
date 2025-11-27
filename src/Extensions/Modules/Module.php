<?php
/**
* The Module Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\App;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;
use Mars\Extensions\Abilities\FilesCacheTrait;
use Mars\Extensions\Modules\Abilities\ConfigTrait;
use Mars\Extensions\Modules\Abilities\LanguagesTrait;
use Mars\Extensions\Modules\Abilities\TemplatesTrait;

/**
 * The Module Class
 * Base class for all module extensions
 */
class Module extends Extension
{
    use FilesCacheTrait;
    use ConfigTrait;
    use LanguagesTrait;
    use TemplatesTrait;

    /**
     * @const array DIR The locations of the used extensions subdirs
     */
    public const array DIRS = [
        'assets' => 'assets',
        'bin' => 'bin',
        'blocks' => 'blocks',
        'config' => 'config',
        'languages' => 'languages',
        'pages' => 'pages',
        'plugins' => 'plugins',
        'routes' => 'routes',
        'templates' => 'templates',
        'setup' => 'setup',
    ];

    /**
     * @internal
     */
    public ?Extensions $manager {
        get => $this->app->modules;
    }

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
}
