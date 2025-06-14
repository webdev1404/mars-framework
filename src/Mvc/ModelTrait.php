<?php
/**
* The Model Trait
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\Hidden;
use Mars\System\Plugins;

/**
 * The Model Trait
 * Coded shared between the Model and ModelItem classes
 */
trait ModelTrait
{
    /**
     * @var Plugins $plugins Alias for $this->app->plugins
     */
    #[Hidden]
    protected Plugins $plugins {
        get => $this->app->plugins;
    }

    /**
     * Builds the Model
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->init();
    }

    /**
     * Inits the model. Method which can be overriden in custom models to init properties etc..
     */
    protected function init()
    {
    }

    /**     
     * {@inheritdoc}
     * @see \Mars\Validation\ValidateTrait::validate()
     */
    public function validate(array|object $data = []) : bool
    {
        if (!$this->validateData($data)) {
            $this->app->errors->add($this->errors);

            return false;
        }

        return true;
    }
}
