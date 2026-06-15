<?php
/**
* The Brotli Compression Class
* @package Mars
*/

namespace Mars\Compression;

/**
 * The Brotli Compression Class
 */
class Brotli implements CompressionInterface
{
    /**
     * @see CompressionInterface::compress()
     * {@inheritDoc}
     */
    public function compress(string $data, ?int $level = null): string
    {
        return \brotli_compress($data, $level ?? BROTLI_COMPRESS_LEVEL_DEFAULT);
    }

    /**
     * @see CompressionInterface::decompress()
     * {@inheritDoc}
     */
    public function decompress(string $data): string
    {
        return \brotli_decompress($data);
    }
}
