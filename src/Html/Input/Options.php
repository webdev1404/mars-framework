<?php
/**
* The Options Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\Html\Tag;
use Mars\Html\TagInterface;

/**
 * The Options Class
 * Renders the select options
 */
class Options extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected static string $tag = 'option';

    /**
     * {@inheritdoc}
     */
    protected static array $empty_attributes = ['value'];

    /**
     * @see TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $options = $attributes['options'] ?? [];
        $selected = (array)($attributes['selected'] ?? []);

        if (!$options) {
            return '';
        }

        $is_list = array_is_list($options);

        $html = '';
        foreach ($options as $value => $text) {
            if (is_array($text)) {
                $optgroup = new Optgroup($this->app);

                $html.= $optgroup->open(['label' => $value]);
                $html.= $this->getOptions($text, $selected);
                $html.= $optgroup->close();
            } else {
                if ($is_list) {
                    $value = $text;
                }

                $html.= parent::html($text, ['value' => $value, 'selected' => in_array($value, $selected)], true);
            }
        }

        return $html;
    }

    /**
     * Returns the html code of the options
     * @param array $options The options
     * @param array $selected The selected options
     * @return string The html code
     */
    protected function getOptions(array $options, array $selected) : string
    {
        $is_list = array_is_list($options);
        
        $html = '';        
        foreach ($options as $value => $text) {
            if ($is_list) {
                $value = $text;
            }

            $html.= parent::html($text, ['value' => $value, 'selected' => in_array($value, $selected)]);
        }

        return $html;
    }
}
