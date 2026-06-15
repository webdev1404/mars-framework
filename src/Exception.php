<?php
/**
* The Exception Class
* @package Mars
*/

namespace Mars;

/**
 * Represents an exception in the application.
 */
class Exception extends \Exception
{
    /**
     * @var string $type The type of the exception.
     */
    public protected(set) string $type;

    /**
     * @var array $data Additional data associated with the exception.
     */
    public protected(set) array $data = [];

    /**
     * Constructs a new Exception object
     * @param string $message The exception message
     * @param string $type The type of the exception
     * @param int $code The exception code
     * @param \Throwable|null $previous The previous exception
     */
    public function __construct(string $message = '', string $type = '', array $data = [], int $code = 0, ?\Throwable $previous = null)
    {
        $this->type = $type;
        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }
}
