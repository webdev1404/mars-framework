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
    public readonly string $type;

    /**
     * Constructs a new Exception object
     * @param string $message The exception message
     * @param string $type The type of the exception
     * @param int $code The exception cod
     * @param \Throwable|null $previous The previous exception
     */
    public function __construct(string $message = '', string $type = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->type = $type;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the type of the exception.
     * @return string The type of the exception.
     */
    public function getType() : string
    {
        return $this->type;
    }
}
