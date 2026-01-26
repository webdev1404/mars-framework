<?php
/**
* The Base Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;

/**
 * The Base Image Class
 */
abstract class Image extends \Mars\Image implements ImageInterface
{
    /**
     * @var bool $valid If true, the image is valid
     */
    public protected(set) bool $valid {
        get {
            if (isset($this->valid)) {
                return $this->valid;
            }

            $this->valid = false;
            if ($this->exists) {
                if ($this->dimensions && $this->type == $this->mime_type) {
                    $this->valid = true;
                }
            }

            return $this->valid;
        }
    }

    /**
     * @var string $mime_type The image's mime type
     */
    protected string $mime_type = '';

    /**
     * @var int $quality The image quality of the resulting images
     */
    protected int $quality = 100;

    /**
     * @var bool $optimize If true, the images will be optimized when processed/uploaded
     */
    protected bool $optimize {
        get => $this->app->config->image->optimize;
    }

    /**
     * @var string $optimize_command The optimization command
     */
    protected string $optimize_command = '';

    /**
     * @see ImageInterface::__construct()
     * {@inheritDoc}
     */
    public function __construct(string $filename, string $open_basedir = '', ?App $app = null)
    {
        parent::__construct($filename, $open_basedir);

        $this->app = $app;
    }

    /**
     * @see ImageInterface::create()
     * {@inheritDoc}
     */
    public function create(int $width, int $height, GdImage $source) : GdImage
    {
        return imagecreatetruecolor($width, $height);
    }

    /**
     * @see ImageInterface::optimize()
     * {@inheritDoc}
     */
    public function optimize() : static
    {
        if (!$this->optimize || !$this->optimize_command) {
            return $this;
        }

        $filename = escapeshellarg($this->filename);
        $command = str_replace('{FILENAME}', $filename, $this->optimize_command);

        exec($command, $output, $result_code);

        if ($result_code !== 0) {
            throw new \Exception("Unable to optimize image {$this->filename}");
        }

        return $this;
    }
}
