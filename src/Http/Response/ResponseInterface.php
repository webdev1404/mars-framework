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
     * @param mixed $content The content to output
     */
    public function output(mixed $content);
}
