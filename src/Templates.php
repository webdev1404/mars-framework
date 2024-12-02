<?php
/**
* The Templates Engine Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Templates\DriverInterface;

/**
 * The Templates Engine Class
 */
class Templates
{
    use InstanceTrait;

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'templates', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var DriverInterface $driver The driver object
     */
    public protected(set) DriverInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->templates_driver);

            return $this->driver;
        }
    }

    /**
     * @var array $supported_drivers The supported drivers
     */
    protected array $supported_drivers = [
        'mars' => \Mars\Templates\Mars::class
    ];

    /**
     * Parses the template content and returns it
     * @param string $content The content to parse
     * @param array $params Params to pass to the parser
     * @return string The parsed content
     */
    public function parse(string $content, array $params) : string
    {
        return $this->driver->parse($content, $params);
    }
}
