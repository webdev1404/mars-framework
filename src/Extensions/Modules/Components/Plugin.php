<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars\Extensions\Modules\Components;

use Mars\App;
use Mars\Extensions\Extensions;

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
    protected static string $base_namespace = "\\Plugins";

    /**
     * @internal
     */
    protected string $manager_class = \Mars\Extensions\Modules\Components\Plugins::class;

    /**
     * @internal
     */
    protected static ?Extensions $manager_instance = null;

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
}
