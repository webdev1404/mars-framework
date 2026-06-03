<?php
/**
 * The Object Route Dispatcher Class
 * @package Mars
 */
namespace Mars\Router\Dispatcher;

use Mars\App\Handlers;
use Mars\Content\ContentInterface;
use Mars\Http\Response\Body\Data\Data;

/**
 * The Object Route Finder Class
 * Handles object routes
 */
class Object_ extends Dispatcher
{
    /**
     * @see Dispatcher::get()
     */
    public function get(mixed $object, array $params): Data
    {
        if ($object instanceof ContentInterface) {
            return $object->run($params);
        } else {
            return $this->app->response->body->create($object);
        }
    }
}
