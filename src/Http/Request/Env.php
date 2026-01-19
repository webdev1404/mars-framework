<?php
/**
* The ENV Class
* @package Mars
*/

namespace Mars\Http\Request;

use Mars\App;

/**
 * The ENV Class
 * Handles the $_ENV interactions
 */
class Env extends Input
{
    /**
     * Constructs the Env object handling $_ENV interactions
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->data = &$_ENV;
    }
}
