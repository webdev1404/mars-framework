<?php
/**
* The Textarea Class
* @package Mars
*/

namespace Mars\Html\Input;

use \Mars\Html\Tag;

/**
 * The Textarea Class
 * Renders a textarea field
 */
class Textarea extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected string $tag = 'textarea';

    /**
     * @see \Mars\Html\TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = [], array $properties = []) : string
    {
        $attributes = $this->generateIdAttribute($attributes);

        return parent::html($text, $attributes, $properties);
    }
}
