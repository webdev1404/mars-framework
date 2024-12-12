<?php
/**
* The Early Hints Preconnect Class
* @package Mars
*/

namespace Mars\Response\Headers\EarlyHints;

use Mars\App\InstanceTrait;
use Mars\Lists\ListSimpleTrait;

/**
 * The Early Hints Preconnect Class
 * Contains the early hints preconnect headers 
 */
class Preconnect
{
    use InstanceTrait;
    use ListSimpleTrait;

    /**
     * Sends the Preconnect headers
     */
    public function send()
    {
        foreach ($this->list as $url) {
            header("Link: <{$url}>; rel=preconnect", false);
        }
    }
}
