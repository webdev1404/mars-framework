<?php
/**
* The System's Theme Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\Templates;

/**
 * The System's Theme Class
 */
class Theme extends \Mars\Extensions\Theme
{
    /**
     * @var bool $is_homepage Set to true if the homepage is currently displayed
     */
    public bool $is_homepage {
        get {
            return $this->app->is_homepage;
        }
        set {}
    }

    /**
     * Builds the theme
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        if (!$app->config->theme) {
            return;
        }
        
        parent::__construct($app->config->theme, $app);

        include($this->path . '/init.php');
    }
}
