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
     * Returns all alerts except those with the specified codes
     * @param string|array $codes The codes to exclude
     * @return array The alerts
     */
    public function getExcept(string|array $codes) : array
    {
        $codes = (array)$codes;
        $alerts = $this->get();

        $alerts = array_filter($alerts, function ($alert) use ($codes) {
            return !in_array($alert['code'], $codes);
        });

        return $alerts;
    }

    /**
     * Adds an alert or multiple alerts to the alerts list.
     * @param string|array|Alerts $alerts The alert(s) text
     * @param string $field An optional field name the alert is related to. Used for form validation errors
     * @param string $code An optional code to identify the alert
     * @return static
     */
    public function add(string|array|Alerts $alerts, string $field = '', string $code = '') : static
    {
        if ($alerts instanceof Alerts) {
            $alerts = $alerts->get();
        } elseif (is_string($alerts)) {
            $alerts = [[
                'text' => $alerts,
                'field' => $field,
                'code' => $code
            ]];
        }
        
        $this->listAdd($alerts);

        return $this;
    }

    /**
     * Resets the alerts then adds the alert(s)
     * @param string|array|Alerts $alerts The alert(s) text
     * @param string $field An optional field name the alert is related to. Used for form validation errors
     * @param string $code An optional code to identify the alert
     * @return static
     */
    public function set(string|array|Alerts $alerts, string $field = '', string $code = '') : static
    {
        return $this->reset()->add($alerts, $field, $code);
    }

    /**
     * Checks if there are any alerts with the specified code
     * @param string $code The code to check for
     * @return bool True if there are alerts with the code, false otherwise
     */
    public function hasCode(string $code) : bool
    {
        return $this->has('code', $code);
    }

    /**
     * Checks if there are any alerts related to the specified field
     * @param string $field The field to check for
     * @return bool True if there are alerts related to the field, false otherwise
     */
    public function hasField(string $field) : bool
    {
        return $this->has('field', $field);
    }

    /**
     * Checks if there are any alerts matching a condition
     * @param string $field The field to check
     * @param string $value The value to check for
     * @return bool True if there are matching alerts, false otherwise
     */
    protected function has(string $field, string $value) : bool
    {
        foreach ($this->alerts as $alert) {
            if ($alert[$field] == $value) {
                return true;
            }
        }

        return false;
    }
}
