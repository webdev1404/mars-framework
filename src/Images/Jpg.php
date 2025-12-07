<?php
/**
* The Jpeg Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;

/**
 * The Jpeg Image Class
 */
class Jpg extends Image implements ImageInterface
{
    /**
     * @var string $mime_type The image's mime type
     */
    protected $mime_type = 'image/jpeg';

    /**
     * @see ImageInterface::__construct()
     * {@inheritdoc}
     */
    public function __construct(string $filename, App $app)
    {
        parent::__construct($filename, $app);

        $this->quality = $this->app->config->image->jpg->quality;
        $this->optimize_command = $this->app->config->image->jpg->optimize_command;
    }

    /**
     * @see ImageInterface::optimize()
     * {@inheritdoc}
     */
    public function optimize() : bool
    {
        $ret = parent::optimize();

        //jpegoptim will reset the file's permissions. Set it to 744
        chmod($this->filename, 0744);

        return $ret;
    }

    /**
     * @see ImageInterface::open()
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function save(GdImage $img)
    {
        if (!imagejpeg($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
