<?php
/**
* The Javascript Library Class
* @package Mars
*/

namespace Mars\Extensions\Libraries;

use Mars\Extensions\Extensions;

/**
 * The Javascript Library Class
 * Handles a single Javascript library
 */
class JavascriptLibrary extends Library
{
    /**
     * @internal
     */
    protected static string $type = 'library-js';

    /**
     * @internal
     */
    protected static string $base_dir = 'libraries/javascript';

    /**
     * @internal
     */
    protected static ?string $development_config_key = 'libraries.js';

    /**
     * @internal
     */
    public protected(set) ?Extensions $manager {
        get => $this->app->libraries->js;
    }
}

