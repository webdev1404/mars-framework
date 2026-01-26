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
     * {@inheritDoc}
     */
    protected static string $tag = 'img';

    /**
     * {@inheritDoc}
     */
    protected static string $newline = '';

    /**
     * {@inheritDoc}
     */
    protected static bool $always_close = false;

    /**
     * @see TagInterface::get()
     * {@inheritDoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        if (empty($attributes['alt'])) {
            $attributes['alt'] = basename($attributes['src'] ?? '');
        }

        return parent::html($text, $attributes);
    }
}
