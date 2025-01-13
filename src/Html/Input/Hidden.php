<?php
/**
* The Hidden Input Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
 * The Hidden Input Class
 * Renders a hidden input field
 */
class Hidden extends Input
{
    /**
     * {@inheritdoc}
     */
    protected static string $type = 'hidden';

    /**
     * @var bool $value_fixed If true, the value will be fixed
     */
    public static bool $value_fixed = true;
}
