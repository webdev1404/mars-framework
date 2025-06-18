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
     * @see \Mars\Response\DriverInterface::output()
     * {@inheritdoc}
     */
    public function output($content)
    {
        echo $content;
    }
}
