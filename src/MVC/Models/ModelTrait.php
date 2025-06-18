<?php
/**
* The Model Trait
* @package Mars
*/

namespace Mars\MVC\Models;

use Mars\App;
use Mars\Config;
use Mars\Hidden;
use Mars\MVC\Controller;
use Mars\System\Plugins;
use Mars\Extensions\Extension;

/**
 * The Model Trait
 * Contains the shared functionality of a model
 */
trait ModelTrait
{
    /**
     * @var Extension $parent The parent extension, if any
     */
    #[Hidden]
    protected ?Extension $parent {
        get => $this->controller->parent ?? null;
    }

    /**
     * @var Controller $controller The controller the model belongs to, if any
     */
    #[Hidden]
    protected ?Controller $controller;

    /**
     * @var Config $config The config object. Alias for $this->app->config
     */
    #[Hidden]
    protected Config $config {
        get => $this->app->config;
    }

    /**
     * @var Plugins $plugins Alias for $this->app->plugins
     */
    #[Hidden]
    protected Plugins $plugins {
        get => $this->app->plugins;
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
        if (!parent::validate($data)) {
            $this->app->errors->add($this->errors);

            return false;
        }

        return true;
    }
}