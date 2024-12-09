<?php
/**
* The Tag Class
* @package Mars
*/

namespace Mars\Html;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Tag Class
 * Renders a generic tag
 */
class Tag implements TagInterface
{
    use InstanceTrait;

    /**
     * @var string $tag The tag's tag
     */
    protected string $tag = '';

    /**
     * @var string $type The tag's attributes, if any
     */
    public array $attributes = [];

    /**
     * @var bool $escape If true, will escape the content
     */
    public bool $escape = true;

    /**
     * @var string $newline Newline to add after the tag, if any
     */
    protected string $newline = "\n";

    /**
     * Opens the tag
     * @param array $attributes The tag's attributes
     * @return string The html code
     */
    public function open(array $attributes = []) : string
    {
        $attributes = $this->app->html->getAttributes($attributes);

        return "<{$this->tag}{$attributes}>" . $this->newline;
    }

    /**
     * Closes the tag
     * @return string The html code
     */
    public function close() : string
    {
        return "</{$this->tag}>" . $this->newline;
    }

    /**
     * @see \Mars\Html\TagInterface::get()
     * {@inheritdoc}
     */
    public function html(string $text = '', array $attributes = [], array $properties = []) : string
    {
        $attributes = $this->app->html->getAttributes($attributes);

        if ($text) {
            $text = $this->escape ? $this->app->escape->html($text) : $text;

            return "<{$this->tag}{$attributes}>" . $text . "</{$this->tag}>" . $this->newline;
        } else {
            return "<{$this->tag}{$attributes}>" . $this->newline;
        }
    }

    /**
     * Generates an id attribute, if one doesn't already exists
     * @param array $attributes The attributes in the format name => value
     * @return array The attributes, including the id field
     */
    public function generateIdAttribute(array $attributes) : array
    {
        if (!isset($attributes['no-id']) && !isset($attributes['id']) && isset($attributes['name'])) {
            $attributes['id'] ??= $this->getIdName($attributes['name']);
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
