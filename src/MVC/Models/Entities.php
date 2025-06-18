<?php
/**
* The Entities Model Class
* @package Mars
*/

namespace Mars\MVC\Models;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\MVC\Controller;

/**
 * The Entities Model Class
 * Implements the Model functionality of the MVC pattern. Represents a collection of entities
 */
abstract class Entities extends \Mars\Entities
{
    use InstanceTrait;
    use ModelTrait;

    /**
     * Builds the Model
     * @param App $app The app object
     * @param Controller|null $controller The controller object, if any
     */
    public function __construct(App $app, ?Controller $controller = null)
    {
        parent::__construct();

        $this->app = $app;
        $this->controller = $controller;

        $this->init();
    }
}
