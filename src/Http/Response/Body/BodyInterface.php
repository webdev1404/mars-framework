<?php
/**
 * The Response Body Interface
* @package Mars
*/

namespace Mars\Http\Response\Body;

/**
 * The Response Body Interface
 */
interface BodyInterface
{
    /**
     * Sends the content as a response body
     * @param mixed $content The content to send
     * @return string The content that was sent
     */
    public function send(mixed $content) : string;
}
