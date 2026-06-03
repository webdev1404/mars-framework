<?php
/**
 * The Base Route Dispatcher Class
 * @package Mars
 */
namespace Mars\Router\Dispatcher;

use Mars\App\Kernel;
use Mars\Http\Response\Body\Data\Data;

/**
 * The Callable Route Dispatcher Class
 * Base class for all route dispatchers
 */
abstract class Dispatcher
{
    use Kernel;

    /**
     * Dispatches the route action and returns the result
     * @param mixed $action The route action to dispatch
     * @param array $params The route parameters
     * @return Data The response data
     */
    abstract public function get(mixed $action, array $params): Data;
}
