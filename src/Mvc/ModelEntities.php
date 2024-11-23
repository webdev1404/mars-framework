<?php
/**
* The Model Item Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Entities;

/**
 * The Model Item Class
 * Implements the Model functionality of the MVC pattern. Extends the Entities class.
 */
abstract class ModelEntities extends Entities
{
    use InstanceTrait;
    use ModelTrait;

    /**
     * Builds the Model
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct();

        $this->app = $app;

        $this->prepare();
        $this->init();
    }
}
