<?php
/**
* The Select Class
* @package Mars
*/

namespace Mars\Html\Input;

use \Mars\Html\Tag;
use \Mars\Html\TagInterface;

/**
 * The Select Class
 * Renders a select field
 */
class Select extends Tag implements FormInputInterface
{
    use FormInputTrait;

    /**
     * @var string $type The tag's type
     */
    public static string $tag = 'select';

    /**
     * The name of the name attribute
     * @var string
     */
    protected static string $name_attribute = 'name';

    /**
     * The name of the value attribute
     * @var string
     */
    protected static string $value_attribute = 'selected';

    /**
     * {@inheritdoc}
     */
    protected static array $properties = ['options', 'selected'];

    /**
     * {@inheritdoc}
     */
    public function open(array $attributes = []) : string
    {
        $attributes['size'] = $attributes['size'] ?? 1;
        $attributes = $this->generateIdAttribute($attributes);

        return parent::open($attributes);
    }

    /**
     * @see TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $options = $attributes['options'] ?? [];
        $selected = (array)($attributes['selected'] ?? []);

        $html = $this->open($attributes);
        $html.= new Options($this->app)->html('', ['options' => $options, 'selected' => $selected]);
        $html.= $this->close();

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedValues(string|array $value, array $attributes) : bool
    {
        $value = (array)$value;
        $values = $attributes['options'] ?? [];
        
        if (array_intersect($value, array_keys($values))) {
            return true;
        }

        return false;
    }
}
