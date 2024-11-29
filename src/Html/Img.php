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
    protected string $tag = 'img';

    /**
     * {@inheritdoc}
     */
    protected string $newline = '';

    /**
     * @see \Mars\Html\TagInterface::get()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = [], array $properties = []) : string
    {
        if (empty($attributes['alt'])) {
            $attributes['alt'] = basename($attributes['src'] ?? '');
        }
        
        return parent::html($text, $attributes, $properties);
    }
}
