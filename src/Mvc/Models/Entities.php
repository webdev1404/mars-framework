<?php
/**
* The Entities Model Class
* @package Mars
*/

namespace Mars\Mvc\Models;

use Mars\App;
use Mars\App\Kernel;
use Mars\Mvc\Controller;

/**
 * The Entities Model Class
 * Implements the Model functionality of the MVC pattern. Represents a collection of entities
 */
abstract class Entities extends \Mars\Entities
{
    use Kernel;
    use ModelTrait;

    /**
     * Builds the Model
     * @param Controller|null $controller The controller object
     * @param App|null $app The app object
     */
    public function __construct(?Controller $controller = null, ?App $app = null)
    {
        parent::__construct();

        $this->app = $app;
        $this->controller = $controller;

        $this->init();
    }
}
