<?php
/**
* The Html Response Class
* @package Mars
*/

namespace Mars\Http\Response\Body;

use Mars\App\Kernel;

/**
 * The Html Response Class
 * Generates a html response
 */
class Html implements BodyInterface
{
    use Kernel;

    /**
     * @see BodyInterface::send()
     * {@inheritDoc}
     */
    public function send(mixed $content) : string
    {
        echo $content;

        return $content;
    }

    /**
     * @see BodyInterface::redirect()
     * {@inheritDoc}
     */
    public function redirect(string $url)
    {
        header('Location: ' . $url);
        die;
    }
}
