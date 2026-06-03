<?php
/**
* The Serializer Driver Interface
* @package Mars
*/

namespace Mars\Serializer;

/**
 * The Serializer Interface
 */
interface SerializerInterface
{
    /**
     * Serializes data
     * @param mixed $data The data to serialize
     * @return string The serialized data
     */
    public function serialize($data) : string;

    /**
     * Unserializes data
     * @param string $data The data to unserialize
     * @return mixed The unserialized data
     */
    public function unserialize(string $data);
}
