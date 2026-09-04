<?php
/**
* The Title Class
* @package Mars
*/

namespace Mars\Document;

use Mars\Document\Tags\MetaTitle;

/**
 * The Title Class
 * Sets both the meta title and the head title of the document
 */
class Title extends MetaTitle
{
    /**
     * Sets the value of the property
     * @param string $value The value
     * @return static
     */
    public function set(string $value) : static
    {
        parent::set($value);

        $this->app->document->meta_title->set($value);
        $this->app->document->heading->set($value);

        return $this;
    }

    /**
     * Renders the title
     */
    public function render()
    {
        //not rendering the title here, as it is rendered in the meta title and heading
    }

    /**
     * Formats a title string by replacing dashes and underscores with spaces and capitalizing each word
     * @param string $title The title to format
     * @return string The formatted title
     */
    public function format(string $title) : string
    {
        $title = str_replace(['-', '_'], ' ', $title);

        return ucwords($title);
    }
}
