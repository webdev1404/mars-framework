<?php
/**
* The Image Class
* @package Mars
*/

namespace Mars;

use GdImage;
use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\App\Handlers;
use Mars\Images\ImageInterface;

/**
 * The Image Class
 */
class Image
{
    use Kernel;

    /**
     * @const array EXTENSIONS The image extensions
     */
    public const array EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'avif'];

    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
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
    public protected(set) array $supported_operations = [
        'resize' => \Mars\Images\Operations\Resize::class,
        'crop' => \Mars\Images\Operations\Crop::class,
        'cut' => \Mars\Images\Operations\Cut::class,
        'convert' => \Mars\Images\Operations\Convert::class,
        'watermark' => \Mars\Images\Operations\Watermark::class
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, ImageInterface::class, 'images', $this->app);
            
            return $this->drivers;
        }
    }

    /**
     * @var Handlers $operations The operations handlers
     */
    public protected(set) Handlers $operations {
        get {
            if (isset($this->operations)) {
                return $this->operations;
            }

            $this->operations = new Handlers($this->supported_operations, null, $this->app);
            $this->operations->store = false;

            return $this->operations;
        }
    }

    /**
     * Returns the image
     * @param string $filename The image's filename
     */
    protected function getImage(string $filename) : ImageInterface
    {
        $ext = $this->app->file->getExtension($filename);
        if (!$ext) {
            throw new \Exception("Invalid image {$filename}");
        }

        return $this->drivers->get($ext, $filename);
    }

    /**
     * Determines if the image is valid
     * @param string $filename The image's filename
     * @return bool
     */
    public function isValid(string $filename) : bool
    {
        if (!$this->drivers->exists($this->app->file->getExtension($filename))) {
            return false;
        }

        return $this->getImage($filename)->isValid();
    }

    /**
     * Returns the size (width/height) of the image
     * @param string $filename The image's filename
     * @return array
     */
    public function getSize(string $filename) : array
    {
        return $this->getImage($filename)->getSize();
    }

    /**
     * Returns the width of the image
     * @param string $filename The image's filename
     * @return int
     */
    public function getWidth(string $filename): int
    {
        return $this->getImage($filename)->getWidth();
    }

    /**
     * Returns the height of the image
     * @param string $filename The image's filename
     * @return int
     */
    public function getHeight(string $filename): int
    {
        return $this->getImage($filename)->getHeight();
    }

    /**
     * Returns the ratio between width and height
     * @param string $filename The image's filename
     * @return float
     */
    public function getRatio(string $filename) : float
    {
        return $this->getImage($filename)->getRatio();
    }

    /**
     * Optimizes the image
     * @param string $filename The image's filename
     * @return bool Returns true, if the image was optimized
     */
    public function optimize(string $filename) : bool
    {
        return $this->getImage($filename)->optimize();
    }

    /**
     * Resizes the image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param int $width The width of the resized image. If 0, it will be computed based on the ratio
     * @param int $height The height of the resized image. If 0, it will be computed based on the ratio
     * @param array $options Resize options, if any
     * @return bool Returns true, if the image was resized
     */
    public function resize(string $filename, string $destination, int $width, int $height = 0, array $options = []) : bool
    {
        $operation = $this->operations->get('resize', $this->getImage($filename), $this->getImage($destination), $this->app);
        $operation->process($width, $height, $options);

        return is_file($destination);
    }

    /**
     * Crops the image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param int $width The width of the cropped image
     * @param int $height The height of the cropped image
     * @param array $options Crop options, if any
     * @return bool Returns true, if the image was cropped
     */
    public function crop(string $filename, string $destination, int $width, int $height, array $options = []) : bool
    {
        $operation = $this->operations->get('crop', $this->getImage($filename), $this->getImage($destination), $this->app);
        $operation->process($width, $height, $options);

        return is_file($destination);
    }

    /**
     * Cuts a section of the image and saves it to destination
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param int $cut_width The width of the cut section
     * @param int $cut_height The height of the cut section
     * @param int $cut_x The x point from where the cut should start
     * @param int $cut_y The y point from where the cut should start
     * @param int $width The width of the resulting image. If 0, the image will have the same width as $cut_width
     * @param int $height The height of the resulting image. If 0 the image will have the same height as $cut_height
     * @param array $options Cut options, if any
     * @return bool Returns true, if the image was cut
     */
    public function cut(string $filename, string $destination, int $cut_width, int $cut_height, int $cut_x = 0, int $cut_y = 0, int $width = 0, int $height = 0, array $options = []) : bool
    {
        $operation = $this->operations->get('cut', $this->getImage($filename), $this->getImage($destination), $this->app);
        $operation->process($cut_width, $cut_height, $cut_x, $cut_y, $width, $height, $options);

        return is_file($destination);
    }

    /**
     * Converts an image to another format
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @return bool Returns true, if the image was converted
     */
    public function convert(string $filename, string $destination) : bool
    {
        $operation = $this->operations->get('convert', $this->getImage($filename), $this->getImage($destination), $this->app);
        $operation->process();

        return is_file($destination);
    }

    /**
     * Places a watermark text over an image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param string $text The text to place as watermark
     * @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Watermark options, if any
     * @return bool Returns true, if the watermarked image was created
     */
    public function watermarkText(string $filename, string $destination, string $text, int $position = 3, array $options = []) : bool
    {
        $operation = $this->operations->get('watermark', $this->getImage($filename), $this->getImage($destination), $this->app);
        $operation->applyText($text, $position, $options);

        return is_file($destination);
    }

    /**
     * Places a watermark image over an image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param string $watermark_image The path of the image which will be used as a watermark
     * @param int $position The position of the watermark image. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Watermark options, if any
     * @return bool Returns true, if the watermarked image was created
     */
    public function watermarkImage(string $filename, string $destination, string $watermark_image, int $position = 3, array $options = []) : bool
    {
        $operation = $this->operations->get('watermark', $this->getImage($filename), $this->getImage($destination), $this->app);
        $operation->applyImage($this->getImage($watermark_image), $position, $options);

        return is_file($destination);
    }
}
