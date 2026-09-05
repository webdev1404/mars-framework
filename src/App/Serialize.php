<?php
/**
* The App Serialize Trait
* @package Mars
*/

namespace Mars\App;

use Mars\App;

/**
 * The App Serialize Trait
 * Trait serializing/unserializing the $app dependency into the current object
 */
trait Serialize
{
    /**
     * Returns the properties of the object, excluding properties with the HiddenProperty attribute or 'get' hooks
     * @return array The properties of the object
     */
    protected function getProperties() : array
    {
        $properties = get_object_vars($this);
        $reflection = new \ReflectionClass($this);

        // Unset properties with the HiddenProperty attribute
        foreach ($properties as $name => $value) {
            if (!$reflection->hasProperty($name)) {
                continue;
            }

            $property = $reflection->getProperty($name);

            if ($property->hasHook(\PropertyHookType::Get)) {
                // Skip properties with a get hook
                unset($properties[$name]);
            }
            
            if ($property->getAttributes(HiddenProperty::class)) {
                unset($properties[$name]);
            }
        }

        return $properties;
    }

    /**
     * Sets the properties of the object from an array
     * @param array $properties The properties to set
     */
    protected function setProperties(array $properties)
    {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Serializes the object, excluding properties with the HiddenProperty attribute
     * @return array The serialized data
     */
    public function __serialize(): array
    {
        return $this->getProperties();
    }

    /**
     * Unserializes the object properties
     * @param array $properties The data to unserialize
     */
    public function __unserialize(array $properties): void
    {
        $this->setProperties($properties);
    }
}
