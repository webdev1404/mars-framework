<?php
/**
* The Model Item Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Entity;
use Mars\Alerts\Errors;
use Mars\Validation\ValidateTrait;

/**
 * The Model Item Class
 * Implements the Model functionality of the MVC pattern. Extends the Entity class.
 */
abstract class ModelEntity extends Entity
{
    use InstanceTrait;
    use ValidateTrait, ModelTrait {
        ValidateTrait::validate as protected validateData;
        ModelTrait::validate insteadof ValidateTrait;
    }

    /**
     * @var Errors $errors The generated errors, if any
     */
    public Errors $errors {
        get {
            if (isset($this->errors)) {
                return $this->errors;
            }

            $this->errors = new Errors($this->app);

            return $this->errors;
        }
    }

    /**
     * @var array $validation_rules Validation rules
     */
    protected static array $validation_rules = [];

    /**
     * @var array $validation_rules_to_skip Validation rules to skip when validating, if any
     */
    protected array $validation_rules_to_skip = [];

    /**
     * @var array $validation_error_strings Custom error strings
     */
    protected static array $validation_error_strings = [];

    /**
     * Builds the Model
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        parent::__construct();

        $this->init();
    }
}
