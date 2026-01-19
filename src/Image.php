<?php
/**
* The Image Class
* @package Mars
*/

namespace Mars;

use Mars\App\Handlers;
use Mars\App\Drivers;
use Mars\Images\ImageInterface;

/**
 * The Image Class
 * Encapsulates methods for working with images
 */
class Image extends File
{
    /**
     * @var array $supported_types The supported image types
     */
    public static array $supported_types = [
        'jpg' => \Mars\Images\Jpg::class,
        'jpeg' => \Mars\Images\Jpg::class,
        'png' => \Mars\Images\Png::class,
        'gif' => \Mars\Images\Gif::class,
        'webp' => \Mars\Images\Webp::class,
        'avif' => \Mars\Images\Avif::class
    ];

    /**
     * @var array $supported_operations The list of supported operations
     */
    public static array $supported_operations = [
        'resize' => \Mars\Images\Operations\Resize::class,
        'crop' => \Mars\Images\Operations\Crop::class,
        'cut' => \Mars\Images\Operations\Cut::class,
        'convert' => \Mars\Images\Operations\Convert::class,
        'watermark' => \Mars\Images\Operations\Watermark::class
    ];

    /**
     * @var ?Drivers $types The image types drivers
     */
    public static ?Drivers $types = null;

    /**
     * @var ?Handlers $operations The operations handlers
     */
    public static ?Handlers $operations = null;

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
                if ($this->dimensions) {
                    $this->valid = true;
                }
            }

            return $this->valid;
        }
    }

    /**
     * @var bool|array $dimensions The image dimensions. False if unable to get dimensions, array with width and height otherwise
     */
    public protected(set) bool|array $dimensions {
        get {
            if (isset($this->dimensions)) {
                return $this->dimensions;
            }

            $this->dimensions = false;
            if ($this->exists) {
                $info = @getimagesize($this->filename);
                if ($info !== false) {
                    $this->dimensions = [$info[0], $info[1]];
                }
            }

            return $this->dimensions;
        }
    }

    /**
     * @var int $width The image width
     */
    public protected(set) int $width {
        get {
            if (isset($this->width)) {
                return $this->width;
            }
            
            $this->width = 0;
            if ($this->dimensions) {
                [$this->width, ] = $this->dimensions;
            }

            return $this->width;
        }
    }

    /**
     * @var int $height The image height
     */
    public protected(set) int $height {
        get {
            if (isset($this->height)) {
                return $this->height;
            }

            $this->height = 0;
            if ($this->dimensions) {
                [, $this->height] = $this->dimensions;
            }

            return $this->height;
        }
    }

    /**
     * @var float $ratio The image ratio
     */
    public protected(set) float $ratio {
        get {
            if (isset($this->ratio)) {
                return $this->ratio;
            }

            $this->ratio = 0;
            if ($this->dimensions) {
                [$width, $height] = $this->dimensions;

                $this->ratio = $width / $height;
            }

            return $this->ratio;
        }
    }

    /**
     * Gets an image type based on the file extension
     * @param string $filename The image's filename
     * @return Image The image object
     */
    public static function getByType(string $filename, string $open_basedir = '') : Image
    {
        $app = App::obj();
        if (!static::$types) {
            static::$types = new Drivers(static::$supported_types, ImageInterface::class, 'images', $app);
        }

        return static::$types->get($app->file->getExtension($filename), $filename, $open_basedir);
    }
    
    /**
     * Gets the operation handler
     * @param string $name The operation name
     * @param Image $source The source image
     * @param Image $destination The destination image
     * @return object The operation handler object
     */
    protected function getOperation(string $name, Image $source, Image $destination) : object
    {
        if (!static::$operations) {
            static::$operations = new Handlers(static::$supported_operations, null, $this->app);
            static::$operations->store = false;
        }

        return static::$operations->get($name, $source, $destination, $this->app);
    }

    /**
     * Optimizes the image
     * @return static
     * @throws \Exception
     */
    public function optimize() : static
    {
        $image = static::getByType($this->filename);
        $image->optimize();

        return $this;
    }

    /**
     * Converts an image to another format
     * @param string $destination The destination's filename
     * @return static
     * @throws \Exception
     */
    public function convert(string $destination) : static
    {
        $source = static::getByType($this->filename);
        $destination = static::getByType($destination);

        $this->getOperation('convert', $source, $destination)->process();

        return $this;
    }

    /**
     * Crops the image
     * @param string $destination The destination's filename
     * @param int $width The width of the cropped image
     * @param int $height The height of the cropped image
     * @param array $options Crop options, if any
     * @return static
     * @throws \Exception
     */
    public function crop(string $destination, int $width, int $height, array $options = []) : static
    {
        $source = static::getByType($this->filename);
        $destination = static::getByType($destination);

        $this->getOperation('crop', $source, $destination)->process($width, $height, $options);

        return $this;
    }

    /**
     * Cuts a section of the image and saves it to destination
     * @param string $destination The destination's filename
     * @param int $cut_width The width of the cut section
     * @param int $cut_height The height of the cut section
     * @param int $cut_x The x point from where the cut should start
     * @param int $cut_y The y point from where the cut should start
     * @param int $width The width of the resulting image. If 0, the image will have the same width as $cut_width
     * @param int $height The height of the resulting image. If 0 the image will have the same height as $cut_height
     * @param array $options Cut options, if any
     * @return static
     * @throws \Exception
     */
    public function cut(string $destination, int $cut_width, int $cut_height, int $cut_x = 0, int $cut_y = 0, int $width = 0, int $height = 0, array $options = []) : static
    {
        $source = static::getByType($this->filename);
        $destination = static::getByType($destination);

        $this->getOperation('cut', $source, $destination)->process($cut_width, $cut_height, $cut_x, $cut_y, $width, $height, $options);

        return $this;
    }

    /**
     * Resizes the image
     * @param string $destination The destination's filename
     * @param int $width The width of the resized image. If 0, it will be computed based on the ratio
     * @param int $height The height of the resized image. If 0, it will be computed based on the ratio
     * @param array $options Resize options, if any
     * @return static
     * @throws \Exception
     */
    public function resize(string $destination, int $width, int $height = 0, array $options = []) : static
    {
        $source = static::getByType($this->filename);
        $destination = static::getByType($destination);

        $this->getOperation('resize', $source, $destination)->process($width, $height, $options);

        return $this;
    }

    /**
     * Places a watermark text over an image
     * @param string $destination The destination's filename
     * @param string $text The text to place as watermark
     * @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Watermark options, if any
     * @return static
     * @throws \Exception
     */
    public function watermarkText(string $destination, string $text, int $position = 3, array $options = []) : static
    {
        $source = static::getByType($this->filename);
        $destination = static::getByType($destination);

        $this->getOperation('watermark', $source, $destination)->applyText($text, $position, $options);

        return $this;
    }

    /**
     * Places a watermark image over an image
     * @param string $destination The destination's filename
     * @param string $watermark_image The path of the image which will be used as a watermark
     * @param int $position The position of the watermark image. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Watermark options, if any
     * @return static
     * @throws \Exception
     */
    public function watermarkImage(string $destination, string $watermark_image, int $position = 3, array $options = []) : static
    {
        $source = static::getByType($this->filename);
        $destination = static::getByType($destination);
        $watermark = static::getByType($watermark_image);

        $this->getOperation('watermark', $source, $destination)->applyImage($watermark, $position, $options);

        return $this;
    }
}
