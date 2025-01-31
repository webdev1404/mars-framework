<?php
/**
* The Ajax Response Class
* @package Mars
*/

namespace Mars\Response\Types;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Ajax Response Class
 * Generates a json response
 */
class Ajax implements DriverInterface
{
    use InstanceTrait;

    /**
     * @see \Mars\Response\DriverInterface::get()
     * {@inheritdoc}
     */
    public function get($content) : string
    {
        return $this->app->json->encode($content);
    }

    /**
     * @see \Mars\Response\DriverInterface::output()
     * {@inheritdoc}
     */
    public function output(string $content)
    {
        header('Content-Type: application/json', true);

        echo $content;
    }
}
