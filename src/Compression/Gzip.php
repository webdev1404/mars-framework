<?php
/**
* The Gzip Compression Class
* @package Mars
*/

namespace Mars\Compression;

/**
 * The Gzip Compression Class
 */
class Gzip implements CompressionInterface
{
    /**
     * @see CompressionInterface::compress()
     * {@inheritDoc}
     */
    public function compress(string $data, ?int $level = null): string
    {
        return \gzencode($data, $level ?? -1);
    }

    /**
     * @see CompressionInterface::decompress()
     * {@inheritDoc}
     */
    public function decompress(string $data): string
    {
        return \gzdecode($data);
    }
}
