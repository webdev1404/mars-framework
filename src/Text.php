<?php
/**
* The Text Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Text Class
 * Text processing functionality
 */
class Text
{
    use Kernel;

    /**
     * Returns the first $max_length characters from text. If strlen($text) > $max_length will append $replace_with
     * @param string $text The text to cut
     * @param int $max_length The max number of characters
     * @param string $replace_with Will replace the removed/cut text with this value
     * @param bool $strip_tags If true will strip the tags of $text
     * @return string The cut text
     */
    public function cut(string $text, int $max_length = 40, string $replace_with = '...', bool $strip_tags = true) : string
    {
        if ($strip_tags) {
            $text = strip_tags($text);
        }

        if (mb_strlen($text) > $max_length) {
            return mb_substr($text, 0, $max_length) . $replace_with;
        } else {
            return $text;
        }
    }

    /**
     * Cuts characters from the middle of $text
     * @param string $text The text to cut
     * @param int $max_length The max number of characters
     * @param string $replace_with Will replace the removed/cut text with this value
     * @param bool $strip_tags If true will strip the tags of $text
     * @return string The cut text
     */
    public function cutMiddle(string $text, int $max_length = 40, string $replace_with = '...', bool $strip_tags = true) : string
    {
        if ($strip_tags) {
            $text = strip_tags($text);
        }

        $count = mb_strlen($text);
        if ($count <= $max_length) {
            return $text;
        }

        $prefix = (int)(ceil($max_length * 2) / 3);
        $suffix = $max_length - $prefix;
        $skip = $count - ($prefix + $suffix);

        return mb_substr($text, 0, $prefix) . $replace_with . mb_substr($text, $prefix + $skip);
    }
}
