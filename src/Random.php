<?php
/**
* The Random Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;

use Random\Randomizer;

/**
 * The Random Class
 * Generates random numbers/strings
 */
class Random
{
    use InstanceTrait;

    /**
     * @var Randomizer $randomizer The randomizer object
     */
    protected Randomizer $randomizer {
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
        $str = bin2hex($this->randomizer->getBytes($max));

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
     * @param int $min Lowest value to be returned
     * @param int $max Highest value to be returned
     * @return int A random number
     */
    public function getFloat(float $min, float $max) : float
    {
        return $this->randomizer->getFloat($min, $max);
    }
}
