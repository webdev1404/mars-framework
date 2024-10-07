<?php
/**
* The DateTime Class
* @package Mars
*/

namespace Mars\Time;

/**
 * The DateTime Class
 * DateTime related functions
 */
class DateTime extends Base
{
    /**
     * {@inheritDoc}
     */
    protected string $format = 'Y-m-d H:i:s';

    /**
     * {@inheritDoc}
     */
    protected string $default_value = '0000-00-00 00:00:00';
}
