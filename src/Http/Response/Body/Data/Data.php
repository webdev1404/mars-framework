<?php
/**
* The Base Class for Response Body Data Classes
* @package Mars
*/

namespace Mars\Http\Response\Body\Data;

/**
 * The Base Class for Response Body Data Classes
 */
abstract class Data
{
    /**
     * @var string $type The type of the data
     */
    public protected(set) string $type = '';

    /**
     * @var mixed $content The content of the data
     */
    public mixed $content;

    /**
     * Builds the response data object
     * @param mixed $content The content of the data
     */
    public function __construct(mixed $content)
    {
        $this->content = $content;
    }
}