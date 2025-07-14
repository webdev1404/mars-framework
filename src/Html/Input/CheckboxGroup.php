<?php
/**
* The Checkbox Group Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\Html\Tag;
use Mars\Html\TagInterface;

/**
 * The Checkbox Group Class
 * Renders a group of checkboxes
 */
class CheckboxGroup extends Tag implements FormInputInterface
{
    use FormInputTrait;

    /**
     * {@inheritdoc}
     */
    protected static array $properties = ['values', 'checked'];

    /**
     * The name of the name attribute
     * @var string
     */
    protected static string $name_attribute = 'name';

    /**
     * The name of the value attribute
     * @var string
     */
    protected static string $value_attribute = 'checked';

    /**
     * @see TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $values = $attributes['values'] ?? [];
        $checked = $attributes['checked'] ?? '';

        if (!$values) {
            return '';
        }

        $input = $this->getInput();

        $html = '';
        foreach ($values as $value => $label) {
            $input_attributes = $attributes;

            $is_checked = $this->isChecked($value, $checked);

            //unset the id attribute if the value is not checked, so it can be generated automatically
            if (!$is_checked) {
                unset($input_attributes['id']);
            }

            $html.= $input->html('', ['value' => $value, 'checked' => $is_checked, 'label' => $label] + $this->getAttributes($input_attributes));
        }

        return $html;
    }

    /**
     * Returns the input to be used
     */
    protected function getInput() : Tag
    {
        return new Checkbox($this->app);
    }

    /**
     * Determines if the value is checked
     * @param string $value The value to check
     * @param string|array $checked The checked value
     * @return bool
     */
    protected function isChecked(string $value, string|array $checked) : bool
    {
        $checked = (array)$checked;
        if (in_array($value, $checked)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedValues(string|array $value, array $attributes) : bool
    {
        $value = (array)$value;
        $values = $attributes['values'] ?? [];

        if (array_intersect($value, array_keys($values))) {
            return true;
        }

        return false;
    }
}
