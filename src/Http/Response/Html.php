<?php
/**
* The Html Response Class
* @package Mars
*/

namespace Mars\Http\Response;

/**
 * The Html Response Class
 * Generates a html response
 */
class Html extends Response implements ResponseInterface
{
    /**
     * @see ResponseInterface::output()
     * {@inheritDoc}
     */
    public function output(mixed $content)
    {
        $this->outputContent($content);
    }
}
