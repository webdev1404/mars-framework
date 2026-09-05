<?php
/**
* The Compression Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Compression\CompressionInterface;

/**
 * The Compression Class
 * Compresses/Decompresses data with gzip, brotli, zstd
 */
class Compression
{
    use Kernel;
    
    /**
     * @var array $drivers_list The supported drivers list
     */
    public protected(set) array $drivers_list = [
        'gzip' => \Mars\Compression\Gzip::class,
        'brotli' => \Mars\Compression\Brotli::class,
        'zstd' => \Mars\Compression\Zstd::class,
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->drivers_list, CompressionInterface::class, 'compression', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var CompressionInterface $driver The driver object
     */
    public protected(set) CompressionInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->driver_objects[$this->app->config->compression->driver] ??= $this->drivers->get($this->app->config->compression->driver);

            return $this->driver;
        }
    }

    /**
     * @var array $driver_objects The instantated driver objects, to avoid instantiating the same driver multiple times
     * @internal
     */
    protected array $driver_objects = [];

    /**
     * Compresses the given data with the specified compression level
     * @param string $data The data to compress
     * @param int|null $level The compression level. If null, the default level of the driver will be used
     * @return string The compressed data
     */
    public function compress(string $data, ?int $level = null): string
    {
        return $this->driver->compress($data, $level ?? $this->app->config->compression->level);
    }

    /**
     * Decompresses the given data
     * @param string $data The data to decompress
     * @return string The decompressed data
     */
    public function decompress(string $data): string
    {
        return $this->driver->decompress($data);
    }

    /**
     * Compresses the given data with the specified driver and compression level
     * @param string $driver The driver to use for compression
     * @param string $data The data to compress
     * @param int|null $level The compression level. If null, the default level of the driver will be used
     * @return string The compressed data
     */
    public function compressWith(string $driver, string $data, ?int $level = null): string
    {
        $driver_obj = $this->driver_objects[$driver] ??= $this->drivers->get($driver);

        return $driver_obj->compress($data, $level ?? $this->app->config->compression->level);
    }

    /**
     * Decompresses the given data with the specified driver
     * @param string $driver The driver to use for decompression
     * @param string $data The data to decompress
     * @return string The decompressed data
     */
    public function decompressWith(string $driver, string $data): string
    {
        $driver_obj = $this->driver_objects[$driver] ??= $this->drivers->get($driver);

        return $driver_obj->decompress($data);
    }
}
