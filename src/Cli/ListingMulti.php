<?php
/**
* The CLI Multi List Printer
* @package Mars
*/

namespace Mars\Cli;

/**
 * The CLI Multi List Printer
 * Displays a list with multiple sections
 */
class ListingMulti extends Listing
{
    /**
     * Prints a list, with multiple sections
     * @param array $data The data to print
     * @param array $colors The colors to use
     * @param array $paddings_right The number of left chars to apply, if any
     * @param array $paddings_left The number of left chars to apply, if any
     */
    public function print(array $data, array $colors = [], array $paddings_right = [], array $paddings_left = [])
    {
        $max = $this->getMaxLength($data, $paddings_right);

        foreach ($data as $header => $list) {
            $this->app->cli->header($header);
            foreach ($list as $text) {
                $this->printMulti($text, $colors, $paddings_left, $max);
            }
            $this->app->cli->printNewline();
        }
    }

    /**
     * @see \Mars\Cli\Base::getMaxLength()
     * {@inheritdoc}
     */
    protected function getMaxLength(array $data, array $paddings_right = []) : array
    {
        $max = [];

        foreach ($data as $list) {
            foreach ($list as $elements) {
                foreach ($elements as $i => $item) {
                    if (!isset($max[$i])) {
                        $max[$i] = 0;
                    }

                    $padding_right = $paddings_right[$i] ?? $this->padding_right;
                    $length = strlen($item) + $padding_right;

                    if ($length > $max[$i]) {
                        $max[$i] = $length;
                    }
                }
            }
        }

        return $max;
    }
}
