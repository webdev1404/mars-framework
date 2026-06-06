<?php
/**
 * The String Route Dispatcher Class
 * @package Mars
 */
namespace Mars\Router\Dispatchers;

use Mars\App\Handlers;
use Mars\Content\ContentInterface;
use Mars\Http\Response\Body\Data\Data;
use Mars\Mvc\Controller;

/**
 * The String Route Finder Class
 * Handles string routes
 */
class String_ extends Dispatcher
{
    /**
     * @see Dispatcher::get()
     */
    public function get(mixed $action, array $params): Data
    {
        $parts = explode('@', $action);

        $class_name = $parts[0];
        $method = $parts[1] ?? '';

        $controller = new $class_name;

        if ($controller instanceof Controller) {
            return $controller->dispatch($method, $params);
        } else {
            if ($method) {
                ob_start();
                $ret = call_user_func_array([$controller, $method], $this->app->reflection->getParams([$controller, $method], $params));
                $content = ob_get_clean();

                return $this->app->response->body->create($ret, $content);
            } else {
                throw new \Exception("No controller method to handle the route for class: {$class_name}");
            }
        }
    }
}

