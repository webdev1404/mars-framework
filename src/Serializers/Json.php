<?php
/**
* The JSON Serializer Class
* @package Mars
*/

namespace Mars\Serializers;

/**
 * The JSON Serializer Class
 */
class Json implements SerializerInterface
{
    /**
     * @see SerializerInterface::serialize()
     * {@inheritDoc}
     */
    public function serialize($data) : string
    {
        return \json_encode($data);
    }

    /**
     * @see SerializerInterface::unserialize()
     * {@inheritDoc}
     */
    public function unserialize(string $data)
    {
        return \json_decode($data);
    }
}
