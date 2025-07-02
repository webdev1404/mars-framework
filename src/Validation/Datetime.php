<?php
/**
* The Datetime Validator Class
* @package Mars
*/

namespace Mars\Validation;

use Mars\App;

/**
 * The Datetime Validator Class
 */
class Datetime extends Rule
{
    /**
     * {@inheritdoc}
     */
    protected string $error = 'validate_datetime_error';

    /**
     * Validates a datetime
     * @param string $value The value to validate
     * @param string $format The datetime's format
     * @param string $format_desc The description of the format
     * @return bool Returns true if the datetime is valid
     */
    public function isValid(string $value, ?string $format = null, ?string $format_desc = null) : bool
    {        
        if ($format) {
            $format_desc ??= $format;
        } else {
            $format = $this->app->lang->datetime_picker_format;
            $format_desc = $this->app->lang->datetime_picker_desc;
        }

        $this->error_replacements = ['{FORMAT}' => $format_desc];

        return $this->isValidDateTime($value, $format);
    }

    /**
     * Determines if $value is a valid DateTime
     * @param string $value The value
     * @param string $format The format
     * @return bool
     */
    protected function isValidDateTime(string $value, ?string $format, ) : bool
    {
        $value = trim($value);

        try {
            $dt = null;
            if ($format) {
                $dt = \DateTime::createFromFormat($format, $value);
            } else {
                $dt = new \DateTime($value);
            }

            if (!$dt) {
                return false;
            }

            $errors = $dt->getLastErrors();
            if(!$errors) {
                return true;
            }
            if ($errors['warning_count'] || $errors['error_count']) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
