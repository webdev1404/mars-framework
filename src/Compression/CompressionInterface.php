<?php
/**
* The Compression Driver Interface
* @package Mars
*/

namespace Mars\Compression;

/**
 * The Compression Interface
 */
interface CompressionInterface
{
    /**
     * Compresses data
     * @param string $data The data to compress
     * @param int|null $level The compression level. If null, the default level of the driver will be used
     * @return string The compressed data
     */
    public function compress(string $data, ?int $level = null): string;

    /**
     * Decompresses data
     * @param string $data The data to decompress
     * @return string The decompressed data
     */
    public function decompress(string $data): string;
}
