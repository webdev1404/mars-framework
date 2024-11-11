<?php
/**
* The SERVER Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
 * The SERVER Class
 * Handles the $_SERVER interactions
 */
class Server extends Input
{
    /**
     * Builds the Server Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->data = &$_SERVER;
    }
}
