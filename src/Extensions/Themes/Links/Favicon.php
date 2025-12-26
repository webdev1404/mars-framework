<?php
/**
 * The Theme's Favicon Url Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

/**
 * The Theme's Favicon Url Class
 */
class Favicon extends Base
{
    /**
     * Loads a favicon
     * @param string|null $image The image to load, if null the default favicon will be loaded
     * @return static
     */
    public function load(?string $image = null) : static
    {
        $image_url = $this->theme->images_url . '/' . ($image ?? 'favicon.png');
        
        $this->app->document->favicon->set($image_url);

        return $this;
    }
}
