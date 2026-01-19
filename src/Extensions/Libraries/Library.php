<?php
/**
* The Library Class
* @package Mars
*/

namespace Mars\Extensions\Libraries;

use Mars\Extensions\Extension;

/**
 * The Library Class
 * Base class for all library classes
 */
abstract class Library extends Extension
{
    /**
     * Boots the library
     */
    public function boot()
    {
        $app = $this->app;

        require($this->path . '/boot.php');
    }
}