<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\Extensions\Root\Module;
use Mars\Extensions\Traits\isInsideAModuleTrait;
use Mars\Extensions\Traits\isSingleClassTrait;
use Mars\Extensions\Abilities\LanguagesTrait;
use Mars\Extensions\Abilities\TemplatesTrait;

/**
 * The Plugin Class
 * Object corresponding to a plugin extension
 */
abstract class Plugin extends Extension
{
    use isInsideAModuleTrait, isSingleClassTrait {
        isInsideAModuleTrait::__construct as __constructModule;
        isSingleClassTrait::__construct as __constructParent;
    }
    use LanguagesTrait;
    use TemplatesTrait;
    
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
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->__constructParent($app);

        $this->app->plugins->addHooks($this, $this->hooks);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRoot() : Base
    {
        return new Module($this->app);
    }
}