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
     * @var string $mime_type The image's mime type
     */
    protected $mime_type = 'image/webp';

    /**
     * ImageInterface::__construct()
     * {@inheritdoc}
     */
    public function __construct(string $filename, App $app)
    {
        parent::__construct($filename, $app);

        $this->quality = $this->app->config->image_webp_quality;
    }

    /**
     * ImageInterface::open()
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function save(GdImage $img)
    {
        if (!imagewebp($img, $this->filename, $this->quality)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
