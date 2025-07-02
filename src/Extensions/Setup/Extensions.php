<?php
/**
* The Base Setup Extension Class
* @package Mars
*/

namespace Mars\Extensions\Setup;

use Mars\App\Kernel;
use Mars\App\Handlers;

/**
 * The Base Setup Extension Class
 * Base class for all basic setup extensions
 */
class Extensions
{
    use Kernel;
    
    public protected(set) array $supported_handlers = [
        'language' => \Mars\Extensions\Setup\Language::class,
        'theme' => \Mars\Extensions\Setup\Theme::class,
        'module' => \Mars\Extensions\Setup\Module::class,
        'plugin' => \Mars\Extensions\Setup\Plugin::class,
    ];

    /**
     * @var Handlers $handlers The handlers object
     */
    public protected(set) Handlers $handlers {
        get {
            if (isset($this->handlers)) {
                return $this->handlers;
            }

            $this->handlers = new Handlers($this->supported_handlers, null, $this->app);

            return $this->handlers;
        }
    }
    
    /**
     * Runs prepare on all supported handlers
     */
    public function prepare()
    {        
        $handlers = $this->handlers->getAll();
        foreach ($handlers as $handler) {
            $handler->prepare();
        }
    }
}