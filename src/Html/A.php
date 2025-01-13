<?php
/**
* The Anchor Class
* @package Mars
*/

namespace Mars\Html;

/**
 * The Anchor Class
 * Renders a link
 */
class A extends \Mars\Html\Tag
{
    /**
     * {@inheritdoc}
     */
    protected static string $tag = 'a';

    /**
     * {@inheritdoc}
     */
    protected static string $newline = '';
}
