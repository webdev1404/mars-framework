<?php
/**
* The Timezone Class
* @package Mars
*/

namespace Mars\Time;

use Mars\App;
use Mars\App\InstanceTrait;
use DateTimeZone;

/**
 * The Timezone Class
 * Time related functions
 */
class Timezone
{
    use InstanceTrait;

    /**
     * @var \DateTimeZone $timezone The timezone applied to the datetime computations
     */
    public protected(set) DateTimeZone $timezone;

    /**
     * Builds the timezone
     * Sets the default timezone to UTC
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->set($this->app->config->timezone);
    }

    /**
     * Sets the timezone
     * @param string $timezone_id The identifier of the timezone to set.
     * @return static
     */
    public function set(string $timezone_id) : static
    {
        date_default_timezone_set($timezone_id);

        $this->timezone = new DateTimeZone($timezone_id);

        return $this;
    }

    /**
     * Resets the timezone to the default setting.
     * @return static
     */
    public function reset() : static
    {
        return $this->set($this->app->config->timezone);
    }
}
