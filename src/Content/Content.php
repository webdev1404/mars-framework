<?php
/**
* The Base Content Class
* @package Mars
*/

namespace Mars\Content;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Base Content Class
 * Base class for content classes
 */
abstract class Content
{
    use Kernel;
    
    /**
     * @var array $data The content's data
     */
    protected array $data = [];
    
    /**
     * Builds the Content object
     * @param array $data The content's data
     * @param App|null $app The app object
     */
    public function __construct(array $data = [], ?App $app = null)
    {
        $this->data = $data;
        $this->app = $app;
    }
}
