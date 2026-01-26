<?php
/**
* The Png Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;

/**
 * The Png Image Class
 */
class Png extends Image implements ImageInterface
{
    /**
     * @internal
     */
    protected string $mime_type = 'image/png';

    /**
     * @internal
     */
    protected int $quality {
        get => $this->app->config->image->png->quality;
    }

    /**
     * @internal
     */
    protected string $optimize_command {
        get => $this->app->config->image->png->optimize_command;
    }

    /**
     * ImageInterface::open()
     * {@inheritDoc}
     */
    public function open() : GdImage
    {
        $img = imagecreatefrompng($this->filename);
        if (!$img) {
            throw new \Exception("Unable to open image {$this->filename}");
        }

        imagealphablending($img, true);
        imagesavealpha($img, true);

        return $img;
    }

    /**
     * ImageInterface::create()
     * {@inheritDoc}
     */
    public function create(int $width, int $height, GdImage $source) : GdImage
    {
        $img = parent::create($width, $height, $source);

        imagealphablending($img, false);
        imagesavealpha($img, true);

        return $img;
    }

    /**
     * ImageInterface::save()
     * {@inheritDoc}
     */
    public function save(GdImage $img)
    {
        if (!imagepng($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
