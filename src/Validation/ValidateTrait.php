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
     * @var array $validation_error_strings Custom error strings
     */
    /*protected static array $validation_error_strings = [];*/

    /**
     * @var array $validation_rules_to_skip Validation rules to skip when validating, if any
     */
    protected array $validation_rules_to_skip = [];

    /**
     * @var array|null $validation_rules_custom Custom validation rules for the current validation
     */
    protected ?array $validation_rules_custom = null;

    /**
     * @var array|null $validation_error_strings_custom Custom error strings for the current validation
     */
    protected ?array $validation_error_strings_custom = null;

    /**
     * Returns the validation rules
     * @return array The rules
     */
    protected function getValidationRules() : array
    {
        return $this->validation_rules_custom ?? (static::$validation_rules ?? []);
    }

    /**
     * Sets the validation rules
     * @param array $validation_rules The rules
     * @return $this
     */
    public function setValidationRules(array $validation_rules) : static
    {
        $this->validation_rules_custom = $validation_rules;

        return $this;
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
     * Sets the validation rules to skip
     * @param array $validation_rules_to_skip The rules to skip
     * @return $this
     */
    protected function setValidationRulesToSkip(array $validation_rules_to_skip) : static
    {
        $this->validation_rules_to_skip = $validation_rules_to_skip;

        return $this;
    }

    /**
     * Returns the validation error strings
     * @return array The error strings
     */
    protected function getValidationErrorStrings() : array
    {
        return $this->validation_error_strings_custom ?? (static::$validation_error_strings ?? []);
    }

    /**
     * Sets the validation error strings
     * @param array $validation_error_strings The error strings
     * @return $this
     */
    public function setValidationErrorStrings(array $validation_error_strings) : static
    {
        $this->validation_error_strings_custom = $validation_error_strings;

        return $this;
    }

    /**
     * The same as skipValidationRules
     * @param string $rule The rule to skip
     * @return static
     */
    public function skipValidationRule(string $rule): static
    {
        return $this->skipValidationRules([$rule]);
    }

    /**
     * Skips rules from validation
     * @param array $skip_rules Rules which will be skipped at validation
     * @return static
     */
    public function skipValidationRules(array $skip_rules): static
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
     * @param array|object $data The data to validate. If empty, the current object ($this) is used
     * @return bool True if the validation passed all tests, false otherwise
     */
    public function validate(array|object $data = []) : bool
    {
        if (!$data) {
            $data = $this;
        }

        $rules = $this->app->plugins->filter('validate.rules', $this->getValidationRules(), $data, $this);
        if (!$rules) {
            return true;
        }

        if (!$this->app->validator->validate($data, $rules, $this->getValidationErrorStrings(), $this->getValidationRulesToSkip())) {
            $this->handleValidationErrors($this->app->validator->getErrors(false));

            return false;
        }

        return $this->app->plugins->filter('validate.after', true, $data, $this->errors, $this);
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
