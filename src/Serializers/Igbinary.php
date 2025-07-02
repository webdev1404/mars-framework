<?php
/**
* The Igbinary Serializer Class
* @package Mars
*/

namespace Mars\Serializers;

/**
 * The Igbinary Driver Interface
 */
class Igbinary implements SerializerInterface
{
    /**
     * @see SerializerInterface::serialize()
     * {@inheritdoc}
     */
    public function serialize($data) : string
    {
        return \igbinary_serialize($data);
    }

    /**
     * @see SerializerInterface::unserialize()
     * {@inheritdoc}
     */
    public function unserialize(string $data)
    {
        return \igbinary_unserialize($data);
    }
}
