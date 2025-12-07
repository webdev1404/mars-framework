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
     * @var string $mime_type The image's mime type
     */
    protected $mime_type = 'image/avif';

    /**
     * @see ImageInterface::__construct()
     * {@inheritdoc}
     */
    public function __construct(string $filename, App $app)
    {
        parent::__construct($filename, $app);

        $this->quality = $this->app->config->image->avif->quality;
    }

    /**
     * @see ImageInterface::open()
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
     * @see ImageInterface::save()
     * {@inheritdoc}
     */
    public function save(GdImage $img)
    {
        if (!imageavif($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
