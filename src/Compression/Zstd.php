<?php
/**
* The Zstd Compression Class
* @package Mars
*/

namespace Mars\Compression;

/**
 * The Zstd Compression Class
 */
class Zstd implements CompressionInterface
{
    /**
     * @see CompressionInterface::compress()
     * {@inheritDoc}
     */
    public function compress(string $data, ?int $level = null): string
    {
        return \zstd_compress($data, $level ?? ZSTD_COMPRESS_LEVEL_DEFAULT);
    }

    /**
     * @see CompressionInterface::decompress()
     * {@inheritDoc}
     */
    public function decompress(string $data): string
    {
        return \zstd_decompress($data);
    }
}
