<?php
/**
* The Preconnect Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Preconnect Urls Class
 * Class containing the preconnect functionality used by a document
 */
class Preconnect extends Urls
{
    /**
     * @see \Mars\Document\Urls::load()
     * {@inheritdoc}
     */
    public function load(string|array $url, string $type = 'head', int $priority = 100, bool $early_hints = false, array $attributes = []) : static
    {
        return parent::load($url, 'preconnect', $priority, false, $attributes);
    }

    /**
     * @see \Mars\Document\Urls::output()
     * {@inheritdoc}
     */
    public function output(string $type = '')
    {
        parent::output('preconnect');
    }

    /**
     * Does nothing
     */
    public function outputUrl(string $url, array $attributes = [])
    {
        echo '<link rel="preconnect" href="'. $this->app->escape->html($url) .'" />';
    }
}
