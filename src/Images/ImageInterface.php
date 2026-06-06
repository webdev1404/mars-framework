<?php
/**
* The Image Driver Interface
* @package Mars
*/

namespace Mars\Images;

use GdImage;

/**
 * The Image Driver Interface
 */
interface ImageInterface
{
    /**
     * Opens the file as a GdImage
     * @return GdImage
     * @throws \Exception
     */
    public function open() : GdImage;

    /**
     * Creates a GdImage object
     * @param int $width The image's width
     * @param int $height The image's height
     * @param GdImage $source The source to create the image from
     * @return GdImage
     */
    public function create(int $width, int $height, GdImage $source) : GdImage;

    /**
     * Saves a GdImage object
     * @param GdImage $img The GdImage object
     * @throws \Exception
     */
    public function save(GdImage $img);

    /**
     * Optimizes the image
     * @return static Returns the current instance
     * @throws \Exception
     */
    public function optimize() : static;
}
