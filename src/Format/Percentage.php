<?php
/**
* The Percentage Format Class
* @package Mars
*/

namespace Mars\Format;

/**
 * The Percentage Format Class
 */
class Percentage
{
    /**
     * @see \Mars\Format::percentage()
     */
    public function format(float $number, float $total, int $decimals = 4) : float
    {
        if (!$number || !$total) {
            return 0;
        }

        $result = ($number * 100) / $total;

        return round($result, $decimals);
    }
}
