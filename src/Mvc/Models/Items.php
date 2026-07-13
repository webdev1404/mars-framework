<?php
/**
* The Items Model Class
* @package Mars
*/

namespace Mars\Mvc\Models;

use Mars\App;
use Mars\Mvc\Controller;

/**
 * The Items Model Class
 * Implements the Model functionality of the MVC pattern. Represents a collection of items
 */
abstract class Items extends \Mars\Items
{
    use ModelTrait;

    /**
     * Builds the Model
     * @param Controller|null $controller The controller object
     * @param App|null $app The app object
     */
    public function __construct(?Controller $controller = null, ?App $app = null)
    {
        parent::__construct(false, $app);

        $this->controller = $controller;

        $this->init();
    }
}
