<?php
/**
* The Avif Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;

/**
 * The Avif Image Class
 */
class Avif extends Image implements ImageInterface
{
    /**
     * @internal
     */
    protected string $mime_type = 'image/avif';

    /**
     * @see ImageInterface::open()
     * {@inheritDoc}
     */
    public function open() : GdImage
    {
        $img = imagecreatefromavif($this->filename);
        if (!$img) {
            throw new \Exception("Unable to open image {$this->filename}");
        }

        return $img;
    }

    /**
     * @see ImageInterface::save()
     * {@inheritDoc}
     */
    public function save(GdImage $img)
    {
        if (!imageavif($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
