<?php
/**
* The H1 Class
* @package Mars
*/

namespace Mars\Document\Tags;

/**
 * The H1 Class
 * Stores the H1 of the document
 */
class H1 extends Tag
{
    /**
     * Renders the H1
     */
    public function render()
    {
        if (!$this->value) {
            return;
        }
        
        echo '<h1>' . $this->app->escape->html($this->value) . '</h1>' . "\n";
    }
}
