<?php
/**
* The Label Class
* @package Mars
*/

namespace Mars\Html\Input;

use \Mars\Html\Tag;

/**
 * The Label Class
 * Renders a label tag
 */
class Label extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected string $tag = 'label';
}
