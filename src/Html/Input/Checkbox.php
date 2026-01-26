<?php
/**
* The Checkbox Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\Html\TagInterface;
use Mars\Html\Label;

/**
 * The Checkbox Class
 * Renders a checkbox
 */
class Checkbox extends Input
{
    /**
     * {@inheritDoc}
     */
    protected static string $type = 'checkbox';

    /**
     * {@inheritDoc}
     */
    protected static array $properties = ['label'];

    /**
     * @see TagInterface::html()
     * {@inheritDoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $label = $attributes['label'] ?? '';

        $attributes = $this->generateIdAttribute($attributes);

        $html = parent::html($text, $attributes);
        if ($label) {
            $html .= new Label($this->app)->html($label, ['for' => $attributes['id'] ?? '']);
        }

        return $html;
    }
}
