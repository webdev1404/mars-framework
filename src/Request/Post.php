<?php
/**
* The POST Request Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
 * The POST Request Class
 * Handles the $_POST interactions
 */
class Post extends Input
{
    /**
     * Builds the Post Request object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->data = &$_POST;
    }
}
