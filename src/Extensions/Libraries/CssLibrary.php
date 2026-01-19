<?php
/**
* The Css Library Class
* @package Mars
*/

namespace Mars\Extensions\Libraries;

use Mars\Extensions\Extensions;

/**
 * The Css Library Class
 * Handles a single CSS library
 */
class CssLibrary extends Library
{
    /**
     * @internal
     */
    protected static string $type = 'library-css';

    /**
     * @internal
     */
    protected static string $base_dir = 'libraries/css';

    /**
     * @internal
     */
    public protected(set) ?Extensions $manager {
        get => $this->app->libraries->css;
    }
}

