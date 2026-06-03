<?php
/**
* The Encoding Class
* @package Mars
*/

namespace Mars\Document\Tag;

/**
 * The Encoding Class
 * Stores the encoding of the document
 */
class Encoding extends Tag
{
    /**
     * @var string $value The property's value
     */
    public string $value {
        get {
            if (isset($this->value)) {
                return $this->value;
            }

            $this->value = $this->app->lang->encoding;

            return $this->value;
        }
    }

    /**
     * Renders the encoding
     */
    public function render()
    {
        $encoding = $this->value;

        $encoding = $this->app->plugins->filter('document.encoding.output', $encoding);

        echo '<meta charset="' . $this->app->escape->html($encoding) . '">' . "\n";
    }
}
