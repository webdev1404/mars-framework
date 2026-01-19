<?php
/**
* The List Class
* @package Mars
*/

namespace Mars\Html\Lists;

use Mars\Html\Tag;
use Mars\Html\TagInterface;

/**
 * The List Class
 * Renders a list
 */
abstract class Listing extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected static array $properties = ['items'];

    /**
     * @see TagInterface::html()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $html = $this->open($this->getAttributes($attributes));
        $html.= $this->getItems($attributes['items']);
        $html.= $this->close();

        return $html;
    }

    /**
     * Returns the item's html code
     * @param array $items The items
     * @return string The html code
     */
    public function getItems(array $items) : string
    {
        $html = '';

        foreach ($items as $item) {
            $html.= "<li>" . $item . "</li>\n";
        }

        return $html;
    }
}
