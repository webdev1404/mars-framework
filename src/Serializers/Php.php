<?php
/**
* The PHP Serializer Class
* @package Mars
*/

namespace Mars\Serializers;

/**
 * The PHP Serializer Class
 */
class Php implements SerializerInterface
{
    /**
     * @see SerializerInterface::serialize()
     * {@inheritdoc}
     */
    public function serialize($data) : string
    {
        return \serialize($data);
    }

    /**
     * @see SerializerInterface::unserialize()
     * {@inheritdoc}
     */
    public function unserialize(string $data)
    {
        return \unserialize($data);
    }
}
