<?php
/**
* The Title Class
* @package Mars
*/

namespace Mars\Document\Tags;

/**
 * The Title Class
 * Stores the title of the document
 */
class Title extends Tag
{
    /**
     * Outputs the title
     */
    public function output()
    {
        $parts = [
            $this->app->config->document->title->prefix,
            $this->value,
            $this->app->config->document->title->suffix
        ];

        $parts = array_filter($parts);

        $title = implode($this->app->config->document->title->separator, $parts);

        $title = $this->app->plugins->filter('document.title.output', $title);

        echo '<title>' . $this->app->escape->html($title) . '</title>' . "\n";
    }
}
