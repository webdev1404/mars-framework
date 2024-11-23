<?php
/**
* The Model Trait
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
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
    protected Plugins $plugins;

    /**
     * Builds the Model
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->prepare();
        $this->init();
    }

    /**
     * Prepares the model's properties
     */
    protected function prepare()
    {
        $this->plugins = $this->app->plugins;
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
