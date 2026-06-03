<?php
/**
* The Favicon Class
* @package Mars
*/

namespace Mars\Document\Tag;

/**
 * The Favicon Class
 * Stores the favicon of the document
 */
class Favicon extends Tag
{
    /**
     * Renders the favicon
     */
    public function render()
    {
        if (!$this->value) {
            return;
        }

        $favicon = $this->app->plugins->filter('document.favicon.output', $this->value, $this);

        echo '<link rel="icon" type="image/png" href="' . $this->app->escape->html($favicon) . '">' . "\n";
    }
}
