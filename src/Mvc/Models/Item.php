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
     * @param App $app The app object
     * @param Controller|null $controller The controller object, if any
     */
    public function __construct(App $app, ?Controller $controller = null)
    {
        parent::__construct([], $app);

        $this->controller = $controller;

        $this->init();
    }
}
