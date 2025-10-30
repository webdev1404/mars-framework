<?php
/**
* The Date Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Date Validator Class
 */
class Date extends Datetime
{
    /**
     * {@inheritdoc}
     */
    public string $error = 'error.validate_date';

    /**
     * @see DateTime::isValid()
     * {@inheritdoc}
     */
    public function isValid(string $value, ?string $format = null, ?string $format_desc = null) : bool
    {
        if ($format) {
            $format_desc ??= $format;
        } else {
            $format = $this->app->lang->date_picker_format;
            $format_desc = $this->app->lang->date_picker_desc;
        }

        return parent::isValid($value, $format, $format_desc);
    }
}
