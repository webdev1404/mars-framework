<?php
/**
* The Convert Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

/**
 * The Convert Operation Image Class
 * Converts an image to another format
 */
class Convert extends Operation
{
    /**
     * Converts the image
     * @throws \Exception
     */
    public function process()
    {
        if (!$this->source->valid) {
            throw new \Exception("Source image {$this->source->filename} is not valid. It either does not exist or is not a valid image.");
        }

        $source_width = $this->source->width;
        $source_height = $this->source->height;

        $this->copyResampled($source_width, $source_height, $source_width, $source_height, 0, 0, $source_width, $source_height, 0, 0, false);

        if (!is_file($this->destination->filename)) {
            throw new \Exception("Failed to create image {$this->destination->filename}");
        }
    }
}
