<?php
/**
* The Convert Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

/**
 * The Crop Operation Image Class
 */
class Convert extends Operation
{
    /**
     * Converts the image
     */
    public function process()
    {
        [$source_width, $source_height] = $this->source->getSize();

        $this->copyResampled($source_width, $source_height, $source_width, $source_height, 0, 0, $source_width, $source_height, 0, 0, false);
    }
}
