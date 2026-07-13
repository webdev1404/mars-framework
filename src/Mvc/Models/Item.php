<?php
/**
* The Item Model Class
* @package Mars
*/

namespace Mars\Mvc\Models;

use Mars\App;
use Mars\Mvc\Controller;

/**
 * The Item Model Class
 * Implements the Model functionality of the MVC pattern. Represents an item
 */
abstract class Item extends \Mars\Item
{
    use ModelTrait;

    /**
     * Builds the Model
     * @param Controller|null $controller The controller object
     * @param App|null $app The app object
     */
    public function __construct(?Controller $controller = null, ?App $app = null)
    {
        parent::__construct([], $app);

        $this->controller = $controller;

        $this->init();
    }
}
