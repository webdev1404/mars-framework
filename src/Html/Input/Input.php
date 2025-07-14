<?php
/**
* The Input Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\Html\Tag;
use Mars\Html\TagInterface;

/**
 * The Input Class
 * Renders an input field
 */
class Input extends Tag implements FormInputInterface
{
    use FormInputTrait;

    /**
     * {@inheritdoc}
     */
    protected static string $tag = 'input';

    /**
     * @var string $type The input's type
     */
    protected static string $type = 'text';

    /**
     * {@inheritdoc}
     */
    protected static bool $always_close = false;

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
     * @see TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $attributes = $this->generateIdAttribute($attributes);

        return parent::html($text, ['type' => static::$type] + $attributes);
    }
}
