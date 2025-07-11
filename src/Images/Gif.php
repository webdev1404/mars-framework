<?php
/**
* The Gif Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;

/**
 * The Gif Image Class
 */
class Gif extends Image implements ImageInterface
{
    /**
     * @var string $mime_type The image's mime type
     */
    protected $mime_type = 'image/gif';

    /**
     * ImageInterface::__construct()
     * {@inheritdoc}
     */
    public function __construct(string $filename, App $app)
    {
        parent::__construct($filename, $app);

        $this->optimize_command = $this->app->config->image_gif_optimize_command;
    }

    /**
     * ImageInterface::open()
     * {@inheritdoc}
     */
    public function open() : GdImage
    {
        $img = imagecreatefromgif($this->filename);
        if (!$img) {
            throw new \Exception("Unable to open image {$this->filename}");
        }

        return $img;
    }

    /**
     * ImageInterface::create()
     * {@inheritdoc}
     */
    public function create(int $width, int $height, GdImage $source) : GdImage
    {
        $img = parent::create($width, $height, $source);

        $originaltransparentcolor = imagecolortransparent($source);

        if ($originaltransparentcolor >= 0 && $originaltransparentcolor < imagecolorstotal($source)) {
            $transparentcolor = imagecolorsforindex($source, $originaltransparentcolor);
            $newtransparentcolor = imagecolorallocate($img, $transparentcolor['red'], $transparentcolor['green'], $transparentcolor['blue']);

            imagefill($img, 0, 0, $newtransparentcolor);
            imagecolortransparent($img, $newtransparentcolor);
        }

        return $img;
    }

    /**
     * ImageInterface::save()
     * {@inheritdoc}
     */
    public function save(GdImage $img)
    {
        if (!imagegif($img, $this->filename)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
