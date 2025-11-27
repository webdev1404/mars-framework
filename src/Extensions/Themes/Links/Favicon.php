<?php
/**
 * The Theme's Fonts Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Fonts Links Class
 * @package Mars
 */
class Favicon extends Link
{
    /**
     * Loads a favicon
     * @param string|null $image The image to load, if null the default favicon will be loaded
     * @return static
     */
    public function load(?string $image = null) : static
    {
        $image_url = $this->theme->images_url . '/' . ($image ?? 'favicon.png');
        
        $this->theme->document->favicon->set($image_url);

        return $this;
    }
}
