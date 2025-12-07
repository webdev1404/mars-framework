<?php
/**
* The Tag Class
* @package Mars
*/

namespace Mars\Html;

use Mars\App\Kernel;

/**
 * The Tag Class
 * Renders a generic tag
 */
class Tag implements TagInterface
{
    use Kernel;

    /**
     * @var string $tag The tag's tag
     */
    protected static string $tag = '';

    /**
     * @var bool $escape If true, will escape the content
     */
    protected static bool $escape = true;

    /**
     * @var string $newline Newline to add after the tag, if any
     */
    protected static string $newline = "\n";

    /**
     * @var string $always_close If true, the tag will always be closed
     */
    protected static bool $always_close = true;

    /**
     * @var array $properties The tag's properties
     */
    protected static array $properties = [];

    /**
     * @var array $empty_properties List with the attributes that will be added even if empty
     */
    protected static array $empty_attributes = [];

    /**
     * Opens the tag
     * @param array $attributes The tag's attributes
     * @return string The html code
     */
    public function open(array $attributes = []) : string
    {
        $attributes = $this->app->html->getAttributes($this->getAttributes($attributes), static::$empty_attributes);

        return '<' . static::$tag . $attributes . '>' . static::$newline;
    }

    /**
     * Closes the tag
     * @return string The html code
     */
    public function close() : string
    {
        return '</' . static::$tag . '>' . static::$newline;
    }

    /**
     * @see \Mars\Html\TagInterface::get()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = []) : string
    {
        $attributes = $this->app->html->getAttributes($this->getAttributes($attributes), static::$empty_attributes);

        if ($text) {
            $text = static::$escape ? $this->app->escape->html($text) : $text;

            return '<' . static::$tag . $attributes . '>' . $text . '</' . static::$tag . '>' . static::$newline;
        } else {
            $html = '<' . static::$tag . $attributes . '>';
            if (static::$always_close) {
                $html.= '</' . static::$tag . '>';
            }
            $html.= static::$newline;

            return $html;
        }
    }

    /**
     * Returns the tag's attributes, with the properties removed
     */
    protected function getAttributes(array $attributes) : array
    {
        if (!static::$properties) {
            return $attributes;
        }

        return $this->app->array->unset($attributes, static::$properties);
    }

    /**
     * Generates an id attribute, if one doesn't already exists
     * @param array $attributes The attributes in the format name => value
     * @return array The attributes, including the id field
     */
    public function generateIdAttribute(array $attributes) : array
    {
        if (isset($attributes['id'])) {
            return $attributes;
        }

        if (!empty($attributes['name'])) {
            $attributes['id'] = $this->getIdName($attributes['name']);
        }

        return $attributes;
    }

    /**
     * Returns an id name for an input field
     * @param string $name The name of the field
     */
    public function getIdName(string $name) : string
    {
        static $id_index = [];

        $name = $this->escapeId($name);

        if (!isset($id_index[$name])) {
            $id_index[$name] = 0;

            return $name;
        } else {
            $id_index[$name]++;

            return $name . '-' . $id_index[$name];
        }
    }

    /**
     * Escapes an ID
     * @param string $id The id to escape
     * @return string The escaped id
     */
    protected function escapeId(string $id) : string
    {
        $id = str_replace(['[', ']', ')', '(', '.', '#'], '', $id);
        $id = str_replace(' ', '-', $id);

        return $id;
    }
}
