<?php
/**
* The Date Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Date Validator Class
 */
class Time extends Datetime
{
    /**
     * {@inheritdoc}
     */
    protected string $error = 'error.validate_time';

    /**
     * @see DateTime::isValid()
     * {@inheritdoc}
     */
    public function isValid(string $value, ?string $format = null, ?string $format_desc = null) : bool
    {
        if ($format) {
            $format_desc ??= $format;
        } else {
            $format = $this->app->lang->time_picker_format;
            $format_desc = $this->app->lang->time_picker_desc;
        }

        return parent::isValid($value, $format, $format_desc);
    }
}
