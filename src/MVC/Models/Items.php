<?php
/**
* The Items Model Class
* @package Mars
*/

namespace Mars\MVC\Models;

use Mars\App;
use Mars\MVC\Controller;

/**
 * The Items Model Class
 * Implements the Model functionality of the MVC pattern. Represents a collection of items
 */
abstract class Items extends \Mars\Items
{
    use ModelTrait;

    /**
     * Builds the Model
     * @param App $app The app object
     * @param Controller|null $controller The controller object, if any
     */
    public function __construct(App $app, ?Controller $controller = null)
    {
        parent::__construct(false, $app);

        $this->controller = $controller;

        $this->init();
    }
}
