<?php
/**
* The Favicon Class
* @package Mars
*/

namespace Mars\Document;

/**
* The Favicon Class
 * Stores the favicon of the document
 */
class Favicon extends Property
{
    /**
     * Outputs the favicon
     */
    public function output()
    {
        if (!$this->value) {
            return;
        }

        $favicon = $this->app->base_url_static . '/' . $this->value;

        $favicon = $this->app->plugins->filter('document_favicon_output', $favicon);

        echo '<link rel="shortcut icon" type="image/png" href="' . $this->app->escape->html($favicon) . '" />' . "\n";
    }
}
