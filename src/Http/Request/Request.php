<?php
/**
* The REQUEST Request Class
* @package Mars
*/

namespace Mars\Http\Request;

use Mars\App;

/**
 * The REQUEST Request Class
 * Handles the $_REQUEST interactions (including $_GET, $_POST, and $_COOKIE)
 */
class Request extends Input
{
    /**
     * Builds the Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->data = &$_REQUEST;
    }
}
