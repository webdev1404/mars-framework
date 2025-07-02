<?php
/**
* The Tag Class
* @package Mars
*/

namespace Mars\Document\Tags;

use Mars\App\Kernel;

/**
 * The Document Tag Class
 * Stores the value of a document's tag. Eg: title
 */
abstract class Tag
{
    use Kernel;

    /**
     * @var string $value The property's value
     */
    public string $value = '';

    /**
     * Returns the value of the property
     * @return string
     */
    public function get() : string
    {
        return $this->value;
    }

    /**
     * Sets the value of the property
     * @param string $value The new value
     * @return static
     */
    public function set(string $value) : static
    {
        $this->value = $value;

        return $this;
    }
}
