<?php
/**
* The Favicon Class
* @package Mars
*/

namespace Mars\Document\Tags;

/**
 * The Favicon Class
 * Stores the favicon of the document
 */
class Favicon extends Tag
{
    /**
     * Outputs the favicon
     */
    public function output()
    {
        if (!$this->value) {
            return;
        }

        $favicon = $this->app->plugins->filter('document.favicon.output', $this->value, $this);

        echo '<link rel="icon" type="image/png" href="' . $this->app->escape->html($favicon) . '">' . "\n";
    }
}
