<?php
/**
* The Textarea Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\Html\Tag;
use Mars\Html\TagInterface;

/**
 * The Textarea Class
 * Renders a textarea field
 */
class Textarea extends Tag implements FormInputInterface
{
    use FormInputTrait;

    /**
     * {@inheritDoc}
     */
    protected static string $tag = 'textarea';

    /**
     * The name of the name attribute
     * @var string
     */
    protected static string $name_attribute = 'name';

    /**
     * The name of the value attribute
     * @var string
     */
    protected static string $value_attribute = 'value';

    /**
     * {@inheritDoc}
     */
    protected static array $properties = ['value'];

    /**
     * @see TagInterface::html()
     * {@inheritDoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        if (!$text) {
            $text = $attributes['value'] ?? '';
        }

        $attributes = $this->generateIdAttribute($attributes);

        return parent::html($text, $attributes);
    }
}
