<?php
/**
 * The Callable Route Dispatcher Class
 * @package Mars
 */
namespace Mars\Router\Dispatcher;

use Mars\App\Handlers;
use Mars\Http\Response\Body\Data\Data;

/**
 * The Callable Route Finder Class
 * Handles callable routes
 */
class Callable_ extends Dispatcher
{
    /**
     * @see Dispatcher::get()
     */
    public function get(mixed $action, array $params): Data
    {
        ob_start();
        $value = call_user_func_array($action, [...$this->app->reflection->getParams($action, $params), $this->app]);
        $content = ob_get_clean();

        if (is_object($value)) {
            //an object was returned, we need to handle it with the object dispatcher
            return $this->app->router->dispatcher->dispatchers->get('object')->get($value, $params);
        }

        return $this->app->response->body->create($value, $content);
    }
}