<?php
/**
* The Radio Group Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\App;
use \Mars\Html\Tag;

/**
 * The Radio Group Class
 * Renders a group of radio buttons
 */
class RadioGroup extends CheckboxGroup
{
    /**
     * {@inheritdoc}
     */
    protected function getInput() : Tag
    {
        return new Radio($this->app);
    }

    /**
     * Determines if the value is checked
     * @param string $value The value to check
     * @param string|array $checked The checked value
     * @return bool
     */
    protected function isChecked(string $value, string|array $checked) : bool
    {
        $checked = App::getString($checked);

        if ($checked == $value) {
            return true;
        }

        return false;
    }
}
