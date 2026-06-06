<?php
/**
* The Routes Dispatcher Class
* @package Mars
*/

namespace Mars\Router;

use Mars\App\Kernel;
use Mars\App\Handlers;
use Mars\Http\Response\Body\Data\Data;

/**
 * The Routes Dispatcher Class
 * Handles the route dispatching
 */
class Dispatcher
{
    use Kernel;

    /**
     * @var array $supported_dispatchers The supported route dispatcher handlers
     */
    public protected(set) array $supported_dispatchers = [
        'callable' => \Mars\Router\Dispatchers\Callable_::class,
        'object' => \Mars\Router\Dispatchers\Object_::class,
        'string' => \Mars\Router\Dispatchers\String_::class,
    ];

    /**
     * @var Handlers $dispatchers The dispatchers object
     */
    public protected(set) Handlers $dispatchers {
        get {
            if (isset($this->dispatchers)) {
                return $this->dispatchers;
            }

            $this->dispatchers = new Handlers($this->supported_dispatchers, null, $this->app);

            return $this->dispatchers;
        }
    }

    /**
     * Dispatches the route action and returns the result
     * @param mixed $action The route action to dispatch
     * @param array $params The route parameters
     * @return array An array containing the dispatched value and content
     */
    public function get(mixed $action, array $params): Data
    {
        $dispatcher = null;

        if (is_callable($action)) {
            $dispatcher = $this->dispatchers->get('callable');
        } elseif (is_object($action)) {
            $dispatcher = $this->dispatchers->get('object');
        } elseif (is_string($action)) {
            $dispatcher = $this->dispatchers->get('string');
        } else {
            throw new \Exception('Unsupported route action type: ' . gettype($action));
        }

        return $dispatcher->get($action, $params, $this->dispatchers);
    }
}