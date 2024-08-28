<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;

/**
 * The Plugins Class
 * Class implementing the Plugins functionality
 */
class Plugins
{
    use \Mars\Extensions\PluginsTrait;

    /**
     * Builds the plugins object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->enabled = $this->app->config->plugins_enable;
    }
}
