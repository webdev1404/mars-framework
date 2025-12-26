<?php
/**
* The Base Alerts Class
* @package Mars
*/

namespace Mars\Alerts;

use Mars\App\Kernel;
use Mars\Data\ListTrait;

/**
 * The Base Alerts Class
 * Container for alerts
 */
abstract class Alerts implements \Countable, \IteratorAggregate
{
    use Kernel;
    use ListTrait {
        ListTrait::add as listAdd;
    }
    
    /**
     * @var array $alerts Array with all the generated alerts
     */
    protected array $alerts = [];

    /**
     * @internal
     */
    protected static string $property = 'alerts';

    /**
     * Adds an alert or multiple alerts to the alerts list.
     * @param string|array|Alerts $alerts The alert(s) text
     * @return static
     */
    public function add(string|array|Alerts $alerts) : static
    {
        if ($alerts instanceof Alerts) {
            $alerts = $alerts->get();
        }
        
        $this->listAdd($alerts);

        return $this;
    }

    /**
     * Resets the alerts then adds the alert(s)
     * @param string|array|Alerts $alerts The alert(s) text
     * @return static
     */
    public function set(string|array|Alerts $alerts) : static
    {
        return $this->reset()->add($alerts);
    }
}
