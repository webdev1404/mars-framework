<?php
/**
* The Early Hints Preload Class
* @package Mars
*/

namespace Mars\Http\Response\Data\Headers\EarlyHints;

use Mars\App\Kernel;
use Mars\Data\ListTrait;

/**
 * The Early Hints Preload Class
 * Contains the early hints preload headers
 */
class Preload
{
    use Kernel;
    use ListTrait;

    /**
     * The allowed types for the preload
     * @var array $allowed_types
     */
    protected array $allowed_types = ['script', 'style', 'font', 'image'];

    /**
     * @var array $urls Array with all the urls to preload
     */
    protected array $urls = [];

    /**
     * @internal
     */
    protected static string $property = 'urls';

    /**
     * Sends the Preload headers
     */
    public function send()
    {
        foreach ($this->allowed_types as $type) {
            $urls = $this->get($type);
            
            if (!$urls) {
                continue;
            }
            
            foreach ($urls as $url) {
                header("Link: <{$url}>; rel=preload; as={$type}", false);
            }
        }
    }
}
