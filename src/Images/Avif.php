<?php
/**
* The Avif Image Class
* @package Mars
*/

namespace Mars\Images;

use Mars\App;
use Mars\Images\Image;
use GdImage;

/**
 * The Avif Image Class
 */
class Avif extends Image implements DriverInterface
{

    /**
     * @var string $mime_type The image's mime type
     */
    protected $mime_type = 'image/avif';

    /**
     * @see \Mars\Images\DriverInterface::__construct()
     * {@inheritdoc}
     */
    public function __construct(string $filename, App $app)
    {
        parent::__construct($filename, $app);

        $this->quality = $this->app->config->image_avif_quality;
    }

    /**
     * @see \Mars\Images\DriverInterface::open()
     * {@inheritdoc}
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
     * @see \Mars\Images\DriverInterface::save()
     * {@inheritdoc}
     */
    public function save(GdImage $img)
    {
        if (!imageavif($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
