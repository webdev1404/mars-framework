<?php
/**
* The Phone Input Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
 * The Phone Input Class
 * Renders a phone input field
 */
class Phone extends Input
{
    /**
     * {@inheritDoc}
     */
    protected static string $type = 'tel';
}
