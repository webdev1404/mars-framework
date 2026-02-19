<?php
/**
* The Model Trait
* @package Mars
*/

namespace Mars\Mvc\Models;

use Mars\App;
use Mars\App\HiddenProperty;
use Mars\Config;
use Mars\Mvc\Controller;
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
    #[HiddenProperty]
    protected ?Extension $parent {
        get => $this->controller?->parent ?? null;
    }

    /**
     * @var Controller $controller The controller the model belongs to, if any
     */
    #[HiddenProperty]
    protected ?Controller $controller;

    /**
     * @var Config $config The config object. Alias for $this->app->config
     */
    #[HiddenProperty]
    protected Config $config {
        get => $this->app->config;
    }

    /**
     * @var Plugins $plugins Alias for $this->app->plugins
     */
    #[HiddenProperty]
    protected Plugins $plugins {
        get => $this->app->plugins;
    }

    /**
     * Inits the model. Method which can be overridden in custom models to init properties etc.
     */
    protected function init()
    {
    }

    /**
     * {@inheritDoc}
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

    /**
     * Alias for $this->app->lang->get()
     */
    protected function __(string $str, array $replace = [], string $prefix = '') : string
    {
        return $this->app->lang->get($str, $replace, $prefix);
    }
}
