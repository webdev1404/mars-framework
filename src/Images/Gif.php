<?php
/**
* The Gif Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;

/**
 * The Gif Image Class
 */
class Gif extends Image implements ImageInterface
{
    /**
     * @internal
     */
    protected string $mime_type = 'image/gif';

    /**
     * @internal
     */
    protected string $optimize_command {
        get => $this->app->config->image->gif->optimize_command;
    }

    /**
     * ImageInterface::open()
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function save(GdImage $img)
    {
        if (!imagegif($img, $this->filename)) {
            throw new \Exception("Unable to save image {$this->filename}");
        }
    }
}
