<?php
/**
* The Json Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;

/**
 * The Json Class
 * Encodes/Decodes data using json
 */
class Json
{
    use InstanceTrait;

    /**
     * Encodes data
     * @param mixed $data The data to encode
     * @return string The encoded string
     */
    public function encode($data) : string
    {
        if (!$data) {
            return '';
        }

        return json_encode($data);
    }

    /**
     * Decodes a string
     * @param string $string The string to decode
     * @param bool $associative If true, the returned data will be an associative array
     * @return mixed The decoded data
     */
    public function decode(string $string, ?bool $associative = true)
    {
        if (!$string) {
            return '';
        }

        return json_decode($string, $associative, 512, JSON_THROW_ON_ERROR);
    }

    public function validate(string $string) : bool
    {
        if (!$string) {
            return false;
        }

        return json_validate($string);
    }
}
