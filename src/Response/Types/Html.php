<?php
/**
* The Html Response Class
* @package Mars
*/

namespace Mars\Response\Types;

use Mars\App\InstanceTrait;

/**
 * The Html Response Class
 * Generates a html response
 */
class Html implements DriverInterface
{
    use InstanceTrait;

    /**
     * @see \Mars\Response\DriverInterface::get()
     * {@inheritdoc}
     */
    public function get($content) : string
    {
        return $content;
    }

    /**
     * @see \Mars\Response\DriverInterface::output()
     * {@inheritdoc}
     */
    public function output(string $content)
    {
        $this->app->response->push->output();

        echo $content;
    }
}
