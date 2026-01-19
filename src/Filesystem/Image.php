<?php
/**
* The Image Class
* @package Mars
*/

namespace Mars\Filesystem;

use Mars\App\Kernel;
use Mars\Image as ImageObj;

/**
 * The Image Class
 */
class Image extends File
{
    use Kernel;

    /**
     * Returns an Image object
     * @param string $filename The filename
     * @return \Mars\Image The Image object
     */
    protected function getObj(string $filename) : ImageObj
    {
        return new ImageObj($filename);
    }

    /**
     * Returns an Image instance based on the file type
     * @param string $filename The filename
     * @return \Mars\Image The Image instance
     */
    public function get(string $filename) : ImageObj
    {
        return ImageObj::getByType($filename);
    }
    
    /**
     * Determines if the image is valid
     * @param string $filename The image's filename
     * @return bool
     */
    public function isValid(string $filename) : bool
    {
        return $this->getObj($filename)->valid;
    }

    /**
     * Returns the dimensions (width/height) of the image
     * @param string $filename The image's filename
     * @return bool|array
     */
    public function getDimensions(string $filename) : bool|array
    {
        return $this->getObj($filename)->dimensions;
    }

    /**
     * Returns the width of the image
     * @param string $filename The image's filename
     * @return int
     */
    public function getWidth(string $filename): int
    {
        return $this->getObj($filename)->width;
    }

    /**
     * Returns the height of the image
     * @param string $filename The image's filename
     * @return int
     */
    public function getHeight(string $filename): int
    {
        return $this->getObj($filename)->height;
    }

    /**
     * Returns the ratio between width and height
     * @param string $filename The image's filename
     * @return float
     */
    public function getRatio(string $filename) : float
    {
        return $this->getObj($filename)->ratio;
    }

    /**
     * Optimizes the image
     * @param string $filename The image's filename
     * @return static
     * @throws \Exception
     */
    public function optimize(string $filename) : static
    {
        $this->getObj($filename)->optimize();

        return $this;
    }

    /**
     * Converts an image to another format
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @return static
     * @throws \Exception
     */
    public function convert(string $filename, string $destination) : static
    {
        $this->getObj($filename)->convert($destination);

        return $this;
    }

    /**
     * Crops the image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param int $width The width of the cropped image
     * @param int $height The height of the cropped image
     * @param array $options Crop options, if any
     * @return static
     * @throws \Exception
     */
    public function crop(string $filename, string $destination, int $width, int $height, array $options = []) : static
    {
        $this->getObj($filename)->crop($destination, $width, $height, $options);

        return $this;
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
     * @return static
     * @throws \Exception
     */
    public function cut(string $filename, string $destination, int $cut_width, int $cut_height, int $cut_x = 0, int $cut_y = 0, int $width = 0, int $height = 0, array $options = []) : static
    {
        $this->getObj($filename)->cut($destination, $cut_width, $cut_height, $cut_x, $cut_y, $width, $height, $options);

        return $this;
    }

    /**
     * Resizes the image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param int $width The width of the resized image. If 0, it will be computed based on the ratio
     * @param int $height The height of the resized image. If 0, it will be computed based on the ratio
     * @param array $options Resize options, if any
     * @return static
     * @throws \Exception
     */
    public function resize(string $filename, string $destination, int $width, int $height = 0, array $options = []) : static
    {
        $this->getObj($filename)->resize($destination, $width, $height, $options);

        return $this;
    }

    /**
     * Places a watermark text over an image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param string $text The text to place as watermark
     * @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Watermark options, if any
     * @return static
     * @throws \Exception
     */
    public function watermarkText(string $filename, string $destination, string $text, int $position = 3, array $options = []) : static
    {
        $this->getObj($filename)->watermarkText($destination, $text, $position, $options);

        return $this;
    }

    /**
     * Places a watermark image over an image
     * @param string $filename The image's filename
     * @param string $destination The destination's filename
     * @param string $watermark_image The path of the image which will be used as a watermark
     * @param int $position The position of the watermark image. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Watermark options, if any
     * @return static
     */
    public function watermarkImage(string $filename, string $destination, string $watermark_image, int $position = 3, array $options = []) : static
    {
        $this->getObj($filename)->watermarkImage($destination, $watermark_image, $position, $options);

        return $this;
    }
}
