<?php
/**
* The Early Hints Preconnect Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers\EarlyHints;

use Mars\App\Kernel;
use Mars\Data\ListTrait;

/**
 * The Early Hints Preconnect Class
 * Contains the early hints preconnect headers
 */
class Preconnect
{
    use Kernel;
    use ListTrait;

    /**
     * @var array $urls Array with all the urls to preload
     */
    protected array $urls = [];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /**
     * Sends the Preconnect headers
     */
    public function send()
    {
        foreach ($this->urls as $url) {
            header("Link: <{$url}>; rel=preconnect", false);
        }
    }
}
