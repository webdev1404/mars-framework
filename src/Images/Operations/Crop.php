<?php
/**
* The Crop Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

/**
 * The Crop Operation Image Class
 */
class Crop extends Operation
{
    /**
     * Crops the image
     * @param int $width The width of the cropped image
     * @param int $height The height of the cropped image
     * @param array $options Options, if any
     * @throws \Exception
     */
    public function process(int $width, int $height, array $options = [])
    {
        if (!$this->source->valid) {
            throw new \Exception("Source image {$this->source->filename} is not valid. It either does not exist or is not a valid image.");
        }

        $source_width = $this->source->width;
        $source_height = $this->source->height;
        $ratio = $width / $height;

        $source_x = 0;
        $source_y = 0;
        $crop_width = 0;
        $crop_height = 0;

        if ($source_width >= $source_height) {
            $crop_width = $source_height * $ratio;
            $crop_height = $source_height;
            $source_x = ($source_width - $crop_width) / 2;

            if ($source_x < 0) {
                $crop_width = $source_width;
                $crop_height = $source_width / $ratio;

                $source_x = 0;
                $source_y = ($source_height - $crop_height) / 2;
            }
        } else {
            $crop_width = $source_width;
            $crop_height = $source_width / $ratio;
            $source_y = ($source_height - $crop_height) / 2;

            if ($source_y < 0) {
                $crop_width = $source_height * $ratio;
                $crop_height = $source_height;

                $source_x = ($source_width - $crop_width) / 2;
                $source_y = 0;
            }
        }

        $this->copyResampled((int) $width, (int) $height, (int) $crop_width, (int) $crop_height, (int)$source_x, (int)$source_y, (int) $width, (int) $height, 0, 0, false, $options);

        if (!is_file($this->destination->filename)) {
            throw new \Exception("Failed to create image {$this->destination->filename}");
        }
    }
}
