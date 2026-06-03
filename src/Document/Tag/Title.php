<?php
/**
* The Title Class
* @package Mars
*/

namespace Mars\Document\Tag;

/**
 * The Title Class
 * Stores the title of the document
 */
class Title extends Tag
{
    /**
     * Renders the title
     */
    public function render()
    {
        $parts = [
            $this->app->config->site->title->prefix,
            $this->value,
            $this->app->config->site->title->suffix
        ];

        $parts = array_filter($parts);

        $title = implode($this->app->config->site->title->separator, $parts);

        $title = $this->app->plugins->filter('document.title.output', $title);

        echo '<title>' . $this->app->escape->html($title) . '</title>' . "\n";
    }
}
