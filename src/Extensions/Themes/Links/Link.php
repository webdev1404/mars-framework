<?php
/**
 * The Theme's Links Base Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\App;
use Mars\App\Kernel;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Links Base Class
 * @package Mars
 */
abstract class Link
{
    use Kernel;
    
    /**
     * The theme object the class is assigned to
     */
    protected Theme $theme;

    /**
     * Builds the Url object
     * @param Theme $theme The theme the url is assigned to
     * @param App $app The app object
     */
    public function __construct(Theme $theme, App $app)
    {
        $this->app = $app;
        $this->theme = $theme;
    }
}
