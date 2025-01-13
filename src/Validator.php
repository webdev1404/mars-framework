<?php
/**
* The Validator Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Alerts\Errors;

/**
 * The Validator Class
 * Validates values
 */
class Validator
{
    use InstanceTrait;

    /**
     * @var array $supported_rules The list of supported validation rules
     */
    protected array $supported_rules = [
        'req' => \Mars\Validators\Required::class,
        'required' => \Mars\Validators\Required::class,
        'unique' => \Mars\Validators\Unique::class,
        'text' => \Mars\Validators\Text::class,
        'string' => \Mars\Validators\Text::class,
        'min' => \Mars\Validators\Min::class,
        'max' => \Mars\Validators\Max::class,
        'int' => \Mars\Validators\IntVal::class,
        'min_int' => \Mars\Validators\MinInt::class,
        'max_int' => \Mars\Validators\MaxInt::class,
        'float' => \Mars\Validators\FloatVal::class,     
        'min_float' => \Mars\Validators\MinFloat::class,
        'max_float' => \Mars\Validators\MaxFloat::class,   
        'interval' => \Mars\Validators\Interval::class,        
        'pattern' => \Mars\Validators\Pattern::class,
        'email' => \Mars\Validators\Email::class,
        'url' => \Mars\Validators\Url::class,
        'ip' => \Mars\Validators\Ip::class,
        'time' => \Mars\Validators\Time::class,
        'date' => \Mars\Validators\Date::class,
        'datetime' => \Mars\Validators\Datetime::class,
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
     * @var Errors $errors The generated errors, if any
     */
    public protected(set) Errors $errors {
        get {
            if (isset($this->errors)) {
                return $this->errors;
            }

            $this->errors = new Errors($this->app);

            return $this->errors;
        }
    }

    /**
     * Checks a value agains a validator
     * @param mixed $value The value to validate
     * @param string $rule The rule to validate the value against
     * @param string $field The name of the field
     * @param mixed $params Extra params to pass to the validator
     * @return bool Returns true if the value is valid
     */
    public function isValid(mixed $value, string $rule, string $field = '', ...$params) : bool
    {
        return $this->rules->get($rule)->validate($value, $field, ...$params);
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
        $this->errors->reset();

        foreach ($rules as $field => $field_rules) {
            if (in_array($field, $skip_array)) {
                continue;
            }

            $value = App::getProperty($data, $field);

            $error_field = $field_rules['field'] ?? $field;
            $rules_array = $field_rules['rules'] ?? $field_rules;

            if (is_string($rules_array)) {
                $rules_array = explode('|', $rules_array);
            }

            foreach ($rules_array as $rule) {
                $parts = explode(':', trim($rule));
                $rule = reset($parts);
                $params = array_slice($parts, 1);

                if (!$this->isValid($value, $rule, $error_field, ...$params)) {
                    $this->addError($rule, $field, $error_strings);
                    $ok = false;
                }
            }
        }

        return $ok;
    }

    /**
     * Adds an error for a field & rule
     * @param string $rule The validation rule name
     */
    protected function addError(string $rule, string $field, array $error_strings)
    {
        //do we have in the $error_strings array a custom error for this rule & $field?
        if ($error_strings && isset($error_strings[$field][$rule])) {
            $this->errors->add(App::__($error_strings[$field][$rule]));

            return;
        }

        //use the rule's error
        $this->errors->add(App::__($this->rules->get($rule)->getError()));
    }

    /**
     * Validates a datetime
     * @param string $value The value to validate
     * @param string $format The datetime's format
     * @return bool Returns true if the datetime is valid
     */
    public function isDatetime(string $value, string $format = null) : bool
    {
        return $this->rules->get('datetime')->isValid($value, $format);
    }

    /**
     * Validates a date
     * @param string $value The value to validate
     * @param string $format The date's format
     * @return bool Returns true if the date is valid
     */
    public function isDate(string $value, string $format = null) : bool
    {
        return $this->rules->get('date')->isValid($value, $format);
    }

    /**
     * Validates a time value
     * @param string $value The value to validate
     * @param string $format The time's format
     * @return bool Returns true if the time value is valid
     */
    public function isTime(string $value, string $format = null) : bool
    {
        return $this->rules->get('time')->isValid($value, $format);
    }

    /**
     * Checks if $value is a valid url
     * @param string $value The value to validate
     * @return bool Returns true if the url is valid
     */
    public function isUrl(string $value) : bool
    {
        return $this->rules->get('url')->isValid($value);
    }

    /**
     * Checks if $value is a valid email address
     * @param string $value The email to validate
     * @return bool Returns true if the email is valid
     */
    public function isEmail(string $value) : bool
    {
        return $this->rules->get('email')->isValid($value);
    }

    /**
     * Checks if $ip is a valid IP address
     * @param string $value The IP to validate
     * @param bool $wildcards If true, the IP can contain wildcards
     * @return bool Returns true if the IP is valid
     */
    public function isIp(string $value, bool $wildcards = false) : bool
    {
        return $this->rules->get('ip')->isValid($value, $wildcards);
    }
}
