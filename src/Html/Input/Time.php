<?php
/**
* The Time Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
 * The Time Class
 * Renders a field from where the time of the day can be picked
 */
class Time extends Input
{
    /**
     * {@inheritdoc}
     */
    protected static string $type = 'time';
}
