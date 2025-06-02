<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\Extensions\Abilities\LanguagesTrait;
use Mars\Extensions\Abilities\TemplatesTrait;

/**
 * The Plugin Class
 * Object corresponding to a plugin extension
 */
abstract class Plugin extends SubModuleSingleFile
{
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
    protected static string $base_namespace = "Plugins";

    /**
     * Builds the plugin
     * @param array $params The params passed to the plugin, if any
     * @param App $app The app object
     */
    public function __construct(array $params = [], App $app)
    {
        parent::__construct($params, $app);

        $this->app->plugins->addHooks($this, $this->hooks);
    }
}