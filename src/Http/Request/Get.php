<?php
/**
* The GET Request Class
* @package Mars
*/

namespace Mars\Http\Request;

use Mars\App;

/**
 * The GET Request Class
 * Handles the $_GET interactions
 */
class Get extends Input
{
    /**
     * Builds the Get Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        
        $this->data = &$_GET;
    }
}
