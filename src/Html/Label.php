<?php
/**
* The Label Class
* @package Mars
*/

namespace Mars\Html;

/**
 * The Label Class
 * Renders a label
 */
class Label extends \Mars\Html\Tag
{
    /**
     * {@inheritdoc}
     */
    protected string $tag = 'label';

    /**
     * {@inheritdoc}
     */
    protected string $newline = '';
}
