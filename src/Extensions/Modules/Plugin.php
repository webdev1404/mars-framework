<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars\Extensions\Modules;

use Mars\App;
use Mars\Extensions\Extension;
use Mars\Extensions\Abilities\LanguagesTrait;
use Mars\Extensions\Abilities\TemplatesTrait;
use Mars\Extensions\List\Plugins as Reader;

/**
 * The Plugin Class
 * Object corresponding to a plugin extension
 */
abstract class Plugin extends Component
{
    /**
     * @internal
     */
    public const array DIRS = [];

    /**
     * @var string $title The plugin's title
     */
    public string $title = '';

    /**
     * @var array $hooks Array listing the defined hooks in the format [hook_name => method]
     */
    protected array $hooks = [];

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
    protected static string $base_namespace = '';

    /**
     * @var array|null $list The list of loaded available extensions of this type
     */
    protected static ?array $list = null;

    /**
     * Builds the extension
     * @param string $module_name The name of the module the extension belongs to
     * @param array $params The params passed to the extension, if any
     * @param App $app The app object
     */
    public function __construct(string $module_name, array $params = [], ?App $app = null)
    {
        parent::__construct($module_name, '', $params, $app);

        $this->app->plugins->addHooks($this, $this->hooks);
    }

    /**
     * Returns the reader object used to read the list of available extensions
     * @param App $app The app object
     * @return object The reader object
     */
    public static function getListReader($app) : object
    {
        return new Reader($app);
    }
}
