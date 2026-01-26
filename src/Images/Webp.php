<?php
/**
* The Webp Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;

/**
 * The Webp Image Class
 */
class Webp extends Image implements ImageInterface
{
    /**
     * @internal
     */
    protected string $mime_type = 'image/webp';

    /**
     * @internal
     */
    protected int $quality {
        get => $this->app->config->image->webp->quality;
    }

    /**
     * ImageInterface::open()
     * {@inheritDoc}
     */
    public function open() : GdImage
    {
        $img = imagecreatefromwebp($this->filename);
        if (!$img) {
            throw new \Exception("Unable to open image {$this->filename}");
        }

        return $img;
    }

    /**
     * ImageInterface::save()
     * {@inheritDoc}
     */
    public function save(GdImage $img)
    {
        if (!imagewebp($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
