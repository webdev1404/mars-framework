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
     * {@inheritdoc}
     * @see FormInputInterface::getValueAttribute()
     */
    public function getNameAttribute() : string
    {
        return static::$name_attribute;
    }

    /**
     * {@inheritdoc}
     * @see FormInputInterface::getValueAttribute()
     */
    public function getValueAttribute() : string
    {
        return static::$value_attribute;
    }

    /**
     * {@inheritdoc}
     * @see FormInputInterface::getAllowedValues()
     */
    public function isAllowedValues(string|array $value, array $attributes) : bool
    {
        return true;
    }
}
