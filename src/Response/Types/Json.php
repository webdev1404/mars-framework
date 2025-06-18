<?php
/**
* The Json Response Class
* @package Mars
*/

namespace Mars\Response\Types;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Json Response Class
 * Generates a json response
 */
class Json implements DriverInterface
{
    use InstanceTrait;

    /**
     * @see \Mars\Response\DriverInterface::output()
     * {@inheritdoc}
     */
    public function output($content)
    {
        header('Content-Type: application/json', true);

        $data = [
            'success' => $this->app->success(),
            'message' => $this->app->messages->getFirst(),
            'error' => $this->app->errors->getFirst(),
            'data' => [...$this->app->json->data, ...App::getArray($content)]
        ];

        echo $this->app->json->encode($data);
    }
}
