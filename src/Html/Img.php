<?php
/**
* The Img Class
* @package Mars
*/

namespace Mars\Html;

/**
 * The Img Class
 * Renders an image
 */
class Img extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected static string $tag = 'img';

    /**
     * {@inheritdoc}
     */
    protected static string $newline = '';

    /**
     * {@inheritdoc}
     */
    protected static bool $always_close = false;

    /**
     * @see TagInterface::get()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        if (empty($attributes['alt'])) {
            $attributes['alt'] = basename($attributes['src'] ?? '');
        }

        return parent::html($text, $attributes);
    }
}
