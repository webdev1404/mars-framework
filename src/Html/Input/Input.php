<?php
/**
* The Input Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\Html\Tag;

/**
 * The Input Class
 * Renders an input field
 */
class Input extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected string $tag = 'input';

    /**
     * @var string $type The input's type
     */
    protected string $type = 'text';

    /**
     * @see \Mars\Html\TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = [], array $properties = []) : string
    {
        $attributes = $this->generateIdAttribute($attributes);

        return parent::html($text, ['type' => $this->type] + $attributes, $properties);
    }
}
