<?php
/**
* The Driver Class
* @package Mars
*/

namespace Mars\App;

use Mars\App;
use Mars\App\Kernel;
use Mars\Data\MapTrait;

/**
 * The Driver Class
 * Encapsulates a list of suported drivers
 */
class Drivers implements \Countable, \IteratorAggregate
{
    use Kernel;
    use MapTrait;

    /**
     * @var string $interface_name The interface the driver must implement
     */
    protected string $interface_name = '';

    /**
     * @var string $config_key The name of the key from where we'll read additional supported drivers from app->config->drivers
     */
    protected string $config_key = '';

    /**
     * @var array $list The list of tags
     */
    protected array $list = [];
    
    /**
     * @internal
     */
    protected static string $property = 'list';

    /**
     * Builds the driver object
     * @param array $list The list of supported drivers
     * @param string $interface_name The interface the driver must implement
     * @param string $config_key The name of the key from where we'll read additional supported drivers from app->config->drivers
     * @param App $app The app object
     */
    public function __construct(array $list, string $interface_name, string $config_key = '', ?App $app = null)
    {
        $this->app = $app ?? App::obj();
        $this->list = $list;
        $this->interface_name = $interface_name;
        $this->config_key = $config_key;
    }

    /**
     * Returns the handle corresponding to the driver
     * @param string $driver The driver's name
     * @param mixed $args Arguments to pass to the handler's constructor
     * @return object The handle
     */
    public function get(string $driver, ...$args) : object
    {
        if ($this->config_key) {
            if (isset($this->app->config->drivers[$this->config_key])) {
                $this->list = $this->app->config->drivers[$this->config_key] + $this->list;
            }
        }

        if (!isset($this->list[$driver])) {
            throw new \Exception("Driver {$driver} is not on the list of supported drivers");
        }

        $class = $this->list[$driver];

        $args[] = $this->app;
        $handle = new $class(...$args);

        if ($this->interface_name) {
            if (!$handle instanceof $this->interface_name) {
                throw new \Exception("Driver {$class} must implement interface {$this->interface_name}");
            }
        }

        return $handle;
    }
}
