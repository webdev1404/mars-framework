<?php
/**
* The Content Interface
* @package Mars
*/

namespace Mars\Content;

/**
 * The Content Interface
 */
interface ContentInterface
{
    /**
     * Outputs the route's content
     * @param array $vars Variables to pass to the content
     */
    public function output(array $vars = []);
}
