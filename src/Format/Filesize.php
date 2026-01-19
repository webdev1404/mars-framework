<?php
/**
* The Filesize Format Class
* @package Mars
*/

namespace Mars\Format;

/**
 * The Filesize Format Class
 */
class Filesize
{
    /**
     * @var int $kb_threshold The KB threshold
     */
    protected int $kb_threshold = 768;

    /**
     * @see \Mars\Format::filesize()
     */
    public function format(int|float $bytes, int $digits = 2) : string
    {
        $kilobytes = $bytes / 1024;

        $mb_limit = 1024 * $this->kb_threshold;

        if ($kilobytes > $mb_limit) {
            return round($kilobytes / 1024 / 1024, $digits) . ' GB';
        } else {
            if ($kilobytes > $this->kb_threshold) {
                return round($kilobytes / 1024, $digits) . ' MB';
            } else {
                return round($kilobytes, $digits) . ' KB';
            }
        }
    }
}
