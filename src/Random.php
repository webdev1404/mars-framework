<?php
/**
* The Random Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Random\Randomizer;

/**
 * The Random Class
 * Generates random numbers/strings
 */
class Random
{
    use Kernel;

    /**
     * @var Randomizer $randomizer The randomizer object
     */
    public protected(set) Randomizer $randomizer {
        get {
            if (isset($this->randomizer)) {
                return $this->randomizer;
            }

            $this->randomizer = new Randomizer;

            return $this->randomizer;
        }
    }

    /**
     * Returns a random string
     * @param int $max The maximum number of chars. the string should have
     * @return string A random string
     */
    public function getString(int $max = 32) : string
    {
        $bytes = (int) ceil($max / 2);
        $str = bin2hex($this->randomizer->getBytes($bytes));

        return substr($str, 0, $max);
    }

    /**
     * Returns a random number
     * @param int $min Lowest value to be returned
     * @param int $max Highest value to be returned
     * @return int A random number
     */
    public function getInt(int $min, int $max) : int
    {
        return $this->randomizer->getInt($min, $max);
    }

    /**
     * Returns a random float
     * @param float $min Lowest value to be returned
     * @param float $max Highest value to be returned
     * @return float A random number
     */
    public function getFloat(float $min, float $max) : float
    {
        return $this->randomizer->getFloat($min, $max);
    }
}
