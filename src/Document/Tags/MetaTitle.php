<?php
/**
* The Meta Title Class
* @package Mars
*/

namespace Mars\Document\Tags;

/**
 * The Meta Title Class
 * Stores the meta title of the document
 */
class MetaTitle extends Tag
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

        $title = $this->app->plugins->filter('document.meta_title.output', $title);

        echo '<title>' . $this->app->escape->html($title) . '</title>' . "\n";
    }
}
