<?php
/**
* The Info Trait
* @package Mars
*/

namespace Mars\App;

/**
 * The Info Trait
 * Removes properties which have the #[HiddenProperty] attribute set from the debug info
 */
trait Info
{
    /**
     * Returns the debug info for the object
     * Hides properties which have the #[HiddenProperty] attribute set
     */
    public function __debugInfo()
    {
        $hidden = [];

        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $attributes = $property->getAttributes('Mars\HiddenProperty');
            if (!empty($attributes)) {
                $hidden[] = $property->getName();
            }
        }

        $properties = get_object_vars($this);
        if ($hidden) {
            foreach ($hidden as $property) {
                unset($properties[$property]);
            }
        }

        return $properties;
    }
}

namespace Mars;

/**
 * Attribute to mark properties which are hidden from the debug info
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class HiddenProperty
{
}
