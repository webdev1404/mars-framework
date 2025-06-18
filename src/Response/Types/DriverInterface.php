<?php
/**
* The Response Interface
* @package Mars
*/

namespace Mars\Response\Types;

/**
 * The Response Interface
 */
interface DriverInterface
{
    /**
     * Outputs $content
     * @param $content The content to output
     */
    public function output($content);
}
