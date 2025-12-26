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
     * @var string $name The content's name
     */
    protected string $name = '';
    
    /**
     * Builds the Content object
     * @param string $name The name of the page/template etc..
     * @param App|null $app The app object
     */
    public function __construct(string $name, ?App $app = null)
    {
        $this->name = $name;
        $this->app = $app;
    }
}
