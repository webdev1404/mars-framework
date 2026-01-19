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
     * {@inheritdoc}
     */
    public function serialize($data) : string
    {
        return \json_encode($data);
    }

    /**
     * @see SerializerInterface::unserialize()
     * {@inheritdoc}
     */
    public function unserialize(string $data)
    {
        return \json_decode($data);
    }
}
