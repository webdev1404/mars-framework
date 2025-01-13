<?php
/**
* The Radio Group Class
* @package Mars
*/

namespace Mars\Html\Input;

use \Mars\Html\Tag;

/**
 * The Checkbox Class
 * Renders a checkbox
 */
class RadioGroup extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected static array $properties = ['values', 'checked'];

    /**
     * @see \Mars\Html\TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $values = $attributes['values'] ?? [];
        $checked = $attributes['checked'] ?? '';

        if (!$values) {
            return '';
        }

        $attributes = $this->getAttributes($attributes);
        
        $radio = new Radio($this->app);

        $html = '';
        foreach ($values as $value => $label) {
            $html.= $radio->html('', ['value' => $value, 'checked' => $value == $checked, 'label' => $label] + $attributes);
        }

        return $html;
    }
}
