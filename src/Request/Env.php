<?php
/**
* The ENV Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
 * The ENV Class
 * Handles the $_ENV interactions
 */
class Env extends Input
{
    /**
     * Builds the Server Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->data = &$_ENV;
    }
}
