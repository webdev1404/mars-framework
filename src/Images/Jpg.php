<?php
/**
* The Jpeg Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;

/**
 * The Jpeg Image Class
 */
class Jpg extends Image implements ImageInterface
{
    /**
     * @internal
     */
    protected string $mime_type = 'image/jpeg';

    /**
     * @internal
     */
    protected int $quality {
        get => $this->app->config->image->jpg->quality;
    }

    /**
     * @internal
     */
    protected string $optimize_command {
        get => $this->app->config->image->jpg->optimize_command;
    }

    /**
     * @see ImageInterface::optimize()
     * {@inheritDoc}
     */
    public function optimize() : static
    {
        parent::optimize();

        //jpegoptim will reset the file's permissions. Set it to 644
        chmod($this->filename, 0644);

        return $this;
    }

    /**
     * @see ImageInterface::open()
     * {@inheritDoc}
     */
    public function open() : GdImage
    {
        $img = imagecreatefromjpeg($this->filename);
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
        if (!imagejpeg($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
