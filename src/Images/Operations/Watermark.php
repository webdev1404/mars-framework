<?php
/**
* The Watermark Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

use Mars\Images\Image;

/**
 * The Watermark Operation Image Class
 */
class Watermark extends Operation
{
    /**
     * Applies a text watermark
     * @param string $text The watermark text
     * @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Options, if any
     * @throws \Exception
     */
    public function applyText(string $text, int $position, array $options = [])
    {
        if (!$this->source->valid) {
            throw new \Exception("Source image {$this->source->filename} is not valid. It either does not exist or is not a valid image.");
        }

        $source_width = $this->source->width;
        $source_height = $this->source->height;

        $source = $this->source->open();
        $destination = $this->destination->create($source_width, $source_height, $source);
        $options = $this->getOptions($options);

        imagecopy($destination, $source, 0, 0, 0, 0, $source_width, $source_height);

        if ($options['text_ttf']) {
            $font_size = imagettfbbox($options['text_size'], $options['text_angle'], $options['text_font'], $text);
            $text_width = $font_size[2] - $font_size[0];
            $text_height = $font_size[3] - $font_size[5];
        } else {
            $text_width = imagefontwidth($options['text_font']) * mb_strlen($text);
            $text_height = imagefontheight($options['text_font']);
        }

        $pos = $this->getPosition($text_width + 2 * $options['padding_left'], $text_height + 2 * $options['padding_top'], $source_width, $source_height, $options['margin_left'], $options['margin_top'], $position);

        //draw the background
        if ($options['background']) {
            $bc = $this->htmlToRgb($options['background']);

            imagefilledrectangle($destination, $pos[0], $pos[1], $pos[0] + $text_width + 2 * $options['padding_left'], $pos[1] + $text_height + 2 * $options['padding_top'], imagecolorallocatealpha($destination, $bc[0], $bc[1], $bc[2], $options['opacity']));
        }

        //draw the text
        $tc = $this->htmlToRgb($options['text_color']);
        if ($options['text_ttf']) {
            imagettftext($destination, $options['text_size'], $options['text_angle'], $pos[0] + $options['padding_left'], $pos[1] + $text_height + $options['padding_top'], imagecolorallocate($destination, $tc[0], $tc[1], $tc[2]), $options['text_font'], $text);
        } else {
            imagestring($destination, $options['text_font'], $pos[0] + $options['padding_left'], $pos[1] + $options['padding_top'], $text, imagecolorallocate($destination, $tc[0], $tc[1], $tc[2]));
        }

        $this->destination->save($destination);

        if (!is_file($this->destination->filename)) {
            throw new \Exception("Failed to create image {$this->destination->filename}");
        }
    }

    /**
     * Applies a watermark image
     * @param Image $watermark The watermark image
     * @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
     * @param array $options Options, if any
     * @throws \Exception
     */
    public function applyImage(Image $watermark, int $position = 3, array $options = [])
    {
        if (!$this->source->valid) {
            throw new \Exception("Source image {$this->source->filename} is not valid. It either does not exist or is not a valid image.");
        }
        if (!$watermark->valid) {
            throw new \Exception("Watermark image {$watermark->filename} is not valid. It either does not exist or is not a valid image.");
        }

        $source_width = $this->source->width;
        $source_height = $this->source->height;
        $watermark_width = $watermark->width;
        $watermark_height = $watermark->height;

        $source = $this->source->open();
        $destination = $this->destination->create($source_width, $source_height, $source);
        $watermark_image = $watermark->open();
        $options = $this->getOptions($options);

        $pos = $this->getPosition($watermark_width, $watermark_height, $source_width, $source_height, $options['margin_left'], $options['margin_top'], $position);

        imagecopy($destination, $source, 0, 0, 0, 0, $source_width, $source_height);

        if ($options['opacity']) {
            //todo; figure out why the transparent areas of the watermark image are rendered as black
            imagecopymerge($destination, $watermark_image, $pos[0], $pos[1], 0, 0, $watermark_width, $watermark_height, $options['opacity']);
        } else {
            imagecopy($destination, $watermark_image, $pos[0], $pos[1], 0, 0, $watermark_width, $watermark_height);
        }

        $this->destination->save($destination);

        if (!is_file($this->destination->filename)) {
            throw new \Exception("Failed to create image {$this->destination->filename}");
        }
    }

    /**
     * Sets the watermark options
     * @param array $options The options array
     * @return array The options
     */
    protected function getOptions(array $options) : array
    {
        $options['background'] ??= $this->app->config->image->watermark->background;
        $options['opacity'] ??= $this->app->config->image->watermark->opacity;
        $options['text_ttf'] ??= $this->app->config->image->watermark->text->ttf;
        $options['text_color'] ??= $this->app->config->image->watermark->text->color;
        $options['text_font'] ??= $this->app->config->image->watermark->text->font;
        $options['text_size'] ??= $this->app->config->image->watermark->text->size;
        $options['text_angle'] ??= $this->app->config->image->watermark->text->angle;
        $options['padding_left'] ??= $this->app->config->image->watermark->padding->left;
        $options['padding_top'] ??= $this->app->config->image->watermark->padding->top;
        $options['margin_left'] ??= $this->app->config->image->watermark->margin->left;
        $options['margin_top'] ??= $this->app->config->image->watermark->margin->top;

        return $options;
    }

    /**
     * Computes the coordinates where the watermark should be placed
     * @param int $watermark_width The watermark's width
     * @param int $watermark_height The watermark's height
     * @param int $image_width The image's width
     * @param int $image_height The image's height
     * @param int $margin_left The left margin
     * @param int $margin_top The top margin
     * @param int $position The watermark's position
     * @return array The x,y position
     */
    protected function getPosition(int $watermark_width, int $watermark_height, int $image_width, int $image_height, int $margin_left, int $margin_top, int $position) : array
    {
        $pos = [];
        switch ($position) {
            case 1:
                $pos = [$margin_left, $image_height - $margin_top - $watermark_height];
                break;
            case 2:
                $pos = [(int)(($image_width - 2 * $margin_left - $watermark_height) / 2), $image_height - $margin_top - $watermark_height];
                break;
            case 3:
                $pos = [$image_width - $margin_left - $watermark_width, $image_height - $margin_top - $watermark_height];
                break;
            case 4:
                $pos = [$margin_left, (int)(($image_height - 2 * $margin_top - $watermark_height) / 2)];
                break;
            case 5:
                $pos = [(int)(($image_width - 2 * $margin_left - $watermark_height) / 2), (int)(($image_height - 2 * $margin_top - $watermark_height) / 2)];
                break;
            case 6:
                $pos = [$image_width - $margin_left - $watermark_width, (int)(($image_height - 2 * $margin_top - $watermark_height) / 2)];
                break;
            case 7:
                $pos = [$margin_left, $margin_top];
                break;
            case 8:
                $pos = [(int)(($image_width - 2 * $margin_left - $watermark_height) / 2), $margin_top];
                break;
            case 9:
                $pos = [$image_width - $margin_left - $watermark_width, $margin_top];
                break;
            default:
                $pos = [$image_width - $margin_left - $watermark_width, $image_height - $margin_top - $watermark_height];
        }

        return $pos;
    }
}
