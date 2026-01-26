<?php
/**
* The Library Class
* @package Mars
*/

namespace Mars\Extensions\Libraries;

use Mars\Extensions\Extension;
use Mars\Document;

/**
 * The Library Class
 * Base class for all library classes
 */
abstract class Library extends Extension
{
    /**
     * @var Document $document The document object
     */
    public Document $document {
        get => $this->app->document;
    }

    /**
     * @var string $assets_path The path to the library assets
     */
    public protected(set) string $assets_path {
        get => $this->path;
    }
    
    /**
     * Boots the library
     */
    public function boot()
    {
        $app = $this->app;

        require($this->path . '/boot.php');
    }
}
