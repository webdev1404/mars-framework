<?php
/**
* The Form Input Trait
* @package Mars
*/

namespace Mars\Html\Input;

/**
 * The Form Input Trait
 */
trait FormInputTrait
{
    /**
     * {@inheritDoc}
     * @see FormInputInterface::getNameAttribute()
     */
    public function getNameAttribute() : string
    {
        return static::$name_attribute;
    }

    /**
     * {@inheritDoc}
     * @see FormInputInterface::getValueAttribute()
     */
    public function getValueAttribute() : string
    {
        return static::$value_attribute;
    }

    /**
     * {@inheritDoc}
     * @see FormInputInterface::isAllowedValue()
     */
    public function isAllowedValue(string|array $value, array $attributes) : bool
    {
        return true;
    }
}
