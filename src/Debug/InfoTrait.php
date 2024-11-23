<?php
/**
* The App Instance Trait
* @package Mars
*/

namespace Mars\Debug;

trait InfoTrait
{
    /**
     * Removes properties which have the #[Hidden] attribute set
     */
    public function __debugInfo()
    {
        $hidden = [];

        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $attributes = $property->getAttributes('Mars\Hidden');
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