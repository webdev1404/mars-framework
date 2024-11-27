<?php
/**
* The Handlers Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Lists\ListObjectsTrait;

/**
 * The Handlers Class
 * Encapsulates a list of suported handlers
 */
class Handlers
{
    use InstanceTrait;
    use ListObjectsTrait;

    /**
     * @var string $interface_name The interface the driver must implement
     */
    public ?string $interface_name = '';

    /**
     * Builds the handler object
     * @param array $list The list of supported handlers
     * @param string $interface_name The interface the handlers must implement, if any
     * @param App $app The app object
     */
    public function __construct(array $list, ?string $interface_name = null, ?App $app = null)
    {
        $this->app = $app ?? $this->getApp();
        $this->interface_name = $interface_name;
        $this->list = $list;
    }
}
