<?php
/**
* The Validator Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Handlers;
use Mars\Alerts\Errors;

/**
 * The Validator Class
 * Validates values
 */
class Validator
{
    use Kernel;

    /**
     * @var array $supported_rules The list of supported validation rules
     */
    public protected(set) array $supported_rules = [
        'req' => \Mars\Validation\Required::class,
        'required' => \Mars\Validation\Required::class,
        'string' => \Mars\Validation\Text::class,
        'text' => \Mars\Validation\Text::class,
        'unique' => \Mars\Validation\Unique::class,
        'min' => \Mars\Validation\Min::class,
        'max' => \Mars\Validation\Max::class,
        'int' => \Mars\Validation\IntVal::class,
        'float' => \Mars\Validation\FloatVal::class,
        'number' => \Mars\Validation\FloatVal::class,
        'pattern' => \Mars\Validation\Pattern::class,
        'email' => \Mars\Validation\Email::class,
        'url' => \Mars\Validation\Url::class,
        'ip' => \Mars\Validation\Ip::class,
        'time' => \Mars\Validation\Time::class,
        'date' => \Mars\Validation\Date::class,
        'datetime' => \Mars\Validation\Datetime::class,
        'captcha' => \Mars\Validation\Captcha::class,
        'username' => \Mars\Validation\Username::class,
        'password' => \Mars\Validation\Password::class
    ];

    /**
     * @var Handlers $rules The rules object
     */
    public protected(set) Handlers $rules {
        get {
            if (isset($this->rules)) {
                return $this->rules;
            }

            $this->rules = new Handlers($this->supported_rules, null, $this->app);

            return $this->rules;
        }
    }

    /**
     * @var array $errors The generated errors, if any, grouped by field
     */
    public protected(set) array $errors = [];

    /**
     * Checks a value against a validator
     * @param mixed $value The value to validate
     * @param string $rule The rule to validate the value against
     * @param mixed $params Extra params to pass to the validator
     * @return bool Returns true if the value is valid
     */
    public function isValid(mixed $value, string $rule, ...$params) : bool
    {
        return $this->rules->get($rule)->validate($value, ...$params);
    }

    /**
     * Validates the rules
     * @param array|object $data The data to validate
     * @param array $rules The rules to validate, in the format ['field' => validation_type]. Eg: 'my_id' => 'required|min:3|unique:my_table:my_id'
     * @param array $error_strings Custom error strings, if any
     * @param array $skip_array Array with the fields for which we'll skip validation, if any
     * @return bool True if the validation passed all tests, false otherwise
     */
    public function validate(array|object $data, array $rules, array $error_strings = [], array $skip_array = []) : bool
    {
        $ok = true;
        $this->errors = [];

        $data = $this->app->array->get($data);

        foreach ($rules as $field => $field_rules) {
            if (in_array($field, $skip_array)) {
                continue;
            }

            $value = $data[$field] ?? '';

            $error_name = $field_rules['name'] ?? $field;
            $rules_array = $field_rules['rules'] ?? $field_rules;
            $break_on_error = $field_rules['break'] ?? true;

            if (is_string($rules_array)) {
                $rules_array = explode('|', $rules_array);
            }

            foreach ($rules_array as $rule) {
                $parts = explode(':', trim($rule));
                $rule = array_first($parts);
                $params = $this->getParams($parts);

                if (!$this->isValid($value, $rule, ...$params)) {
                    $this->addError($rule, $field, $error_name, $error_strings);
                    $ok = false;

                    if ($break_on_error) {
                        break;
                    }
                }
            }
        }

        return $ok;
    }

    /**
     * Returns the params for a rule
     * @param array $parts The parts of the rule, split by ':'
     * @return array The params for the rule
     */
    protected function getParams(array $parts) : array
    {
        $params = array_slice($parts, 1);

        if ($params) {
            //convert 'null' to null
            array_walk($params, function (&$param) {
                if ($param == 'null' || $param == '') {
                    $param = null;
                }
            });
        }

        return $params;
    }

    /**
     * Adds an error for a field & rule
     * @param string $rule The validation rule name
     */
    protected function addError(string $rule, string $field, string $error_name, array $error_strings)
    {
        $this->errors[$field] = $this->errors[$field] ?? [];

        //do we have in the $error_strings array a custom error for this rule & $field?
        if ($error_strings && isset($error_strings[$field][$rule])) {
            $this->errors[$field][] = App::__($error_strings[$field][$rule]);

            return;
        }

        //use the rule's error
        $this->errors[$field][] = $this->rules->get($rule)->getError($field, $error_name);
    }

    /**
     * Returns the errors
     * @param bool $flat If true, merges the errors into a single error, with $separator between them
     * @param string $separator The separator to use between errors if $flat is true
     * @return array The errors
     */
    public function getErrors(bool $flat = true, string $separator = '<br>') : array
    {
        $errors = [];
        foreach ($this->errors as $field => $field_errors) {

            if ($flat) {
                $errors[$field] = implode($separator, $field_errors);
            } else {
                foreach ($field_errors as $error) {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    /**
     * Validates a datetime
     * @param string $datetime The datetime to validate
     * @param string $format The datetime's format
     * @return bool Returns true if the datetime is valid
     */
    public function isDatetime(string $datetime, ?string $format = null) : bool
    {
        return $this->rules->get('datetime')->isValid($datetime, $format);
    }

    /**
     * Validates a date
     * @param string $date The date to validate
     * @param string $format The date's format
     * @return bool Returns true if the date is valid
     */
    public function isDate(string $date, ?string $format = null) : bool
    {
        return $this->rules->get('date')->isValid($date, $format);
    }

    /**
     * Validates a time value
     * @param string $time The time to validate
     * @param string $format The time's format
     * @return bool Returns true if the time value is valid
     */
    public function isTime(string $time, ?string $format = null) : bool
    {
        return $this->rules->get('time')->isValid($time, $format);
    }

    /**
     * Checks if $url is a valid url
     * @param string $url The url to validate
     * @return bool Returns true if the url is valid
     */
    public function isUrl(string $url) : bool
    {
        return $this->rules->get('url')->isValid($url);
    }

    /**
     * Checks if $email is a valid email address
     * @param string $email The email to validate
     * @return bool Returns true if the email is valid
     */
    public function isEmail(string $email) : bool
    {
        return $this->rules->get('email')->isValid($email);
    }

    /**
     * Checks if $ip is a valid IP address
     * @param string $ip The IP to validate
     * @param bool $wildcards If true, the IP can contain wildcards
     * @return bool Returns true if the IP is valid
     */
    public function isIp(string $ip, bool $wildcards = false) : bool
    {
        return $this->rules->get('ip')->isValid($ip, $wildcards);
    }
}
