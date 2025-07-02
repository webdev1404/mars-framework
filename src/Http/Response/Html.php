<?php
/**
* The Html Response Class
* @package Mars
*/

namespace Mars\Http\Response;

use Mars\App\Kernel;

/**
 * The Html Response Class
 * Generates a html response
 */
class Html implements ResponseInterface
{
    use Kernel;

    /**
     * @see ResponseInterface::output()
     * {@inheritdoc}
     */
    public function output($content)
    {
        echo $content;
    }
}
