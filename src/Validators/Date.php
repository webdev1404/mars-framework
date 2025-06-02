<?php
/**
* The Date Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
 * The Date Validator Class
 */
class Date extends Datetime
{
    /**
     * {@inheritdoc}
     */
    protected string $error = 'validate_date_error';

    /**
     * @see \Mars\Validators\DateTime::isValid()
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
