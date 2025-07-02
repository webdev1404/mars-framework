<?php
/**
* The Response Interface
* @package Mars
*/

namespace Mars\Http\Response;

/**
 * The Response Interface
 */
interface ResponseInterface
{
    /**
     * Outputs $content
     * @param $content The content to output
     */
    public function output($content);
}
