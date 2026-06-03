<?php
/**
* The Igbinary Serializer Class
* @package Mars
*/

namespace Mars\Serializer;

/**
 * The Igbinary Serializer Class
 */
class Igbinary implements SerializerInterface
{
    /**
     * @see SerializerInterface::serialize()
     * {@inheritDoc}
     */
    public function serialize($data) : string
    {
        return \igbinary_serialize($data);
    }

    /**
     * @see SerializerInterface::unserialize()
     * {@inheritDoc}
     */
    public function unserialize(string $data)
    {
        return \igbinary_unserialize($data);
    }
}
