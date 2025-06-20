<?php
/**
* The Validate Trait
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Validate Trait
 * Provides validation functionality
 * Classes using this trait must set these properties:
 * public Errors $errors;
 * protected static array $validation_rules = [];
 * protected static array $validation_error_strings = [];
 * protected array $validation_rules_to_skip = [];
 */
trait ValidateTrait
{
    /**
     * @var Errors $errors The generated errors, if any
     */
    /*public Errors $errors;*/

    /**
     * @var array $validation_rules Validation rules
     */
    /*protected static array $validation_rules = [];*/

    /**
     * @var array $validation_rules_to_skip Validation rules to skip when validating, if any
     */
    //protected array $validation_rules_to_skip = [];

    /**
     * @var array $validation_error_strings Custom error strings
     */
    /*protected array static $validation_error_strings = [];*/

    /**
     * Returns the validation rules
     * @return array The rules
     */
    protected function getValidationRules() : array
    {
        return static::$validation_rules ?? [];
    }

    /**
     * Returns the validation rules to skip
     * @return array The rules to skip
     */
    protected function getValidationRulesToSkip() : array
    {
        return $this->validation_rules_to_skip ?? [];
    }

    /**
     * Returns the validation error strings
     * @return array The error strings
     */
    protected function getValidationErrorStrings() : array
    {
        return static::$validation_error_strings ?? [];
    }

    /**
     * The same as skipValidationRules
     * @param string $rule The rule to skip
     * @return $this
     */
    public function skipValidationRule(string $rule)
    {
        return $this->skipValidationRules([$rule]);
    }

    /**
     * Skips rules from validation
     * @param array $skip_rules Rules which will be skipped at validation
     * @return $this
     */
    public function skipValidationRules(array $skip_rules)
    {
        foreach ($skip_rules as $rule) {
            if (!in_array($rule, $this->validation_rules_to_skip)) {
                $this->validation_rules_to_skip[] = $rule;
            }
        }

        return $this;
    }

    /**
     * Validates the data
     * @param array|object $data The data to validate. If empty, the $_POST data is used
     * @return bool True if the validation passed all tests, false otherwise
     */
    public function validate(array|object $data = []) : bool
    {
        if (!$data) {
            $data = $this;
        }

        $rules = $this->getValidationRules();
        if (!$rules) {
            return true;
        }

        if (!$this->app->validator->validate($data, $rules, $this->getValidationErrorStrings(), $this->getValidationRulesToSkip())) {
            $this->handleValidationErrors($this->app->validator->getErrors(false));

            return false;
        }

        return true;
    }

    /**
     * Returns the validation errors
     * @return array The validation errors
     */
    public function getValidationErrors() : array
    {
        return $this->app->validator->getErrors();
    }

    /**
     * Handles validation errors
     * @param array $errors The errors to handle
     */
    public function handleValidationErrors(array $errors)
    {
        $this->errors->set($errors);

        if ($this->app->request->is_json) {
            $this->app->json->add('validation_errors', $this->app->validator->getErrors());
        }
    }
}
