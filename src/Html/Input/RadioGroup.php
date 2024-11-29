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
     * @see \Mars\Html\TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = [], array $properties = []) : string
    {
        $values = $properties['values'] ?? [];
        $checked = $properties['checked'] ?? '';

        if (!$values) {
            return '';
        }
        
        $radio = new Radio($this->app);

        $html = '';
        foreach ($values as $value => $label) {
            $html.= $radio->html('', ['value' => $value, 'checked' => $value == $checked] + $attributes, ['label' => $label]);
        }

        return $html;
    }
}
