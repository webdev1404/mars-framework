<?php
/**
* The Timer Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Timer Class
 * Contains timer functionality
 */
class Timer
{
    use Kernel;
    
    /**
     * @var float $start The time when the script was started
     */
    public float $start {
        get => $_SERVER['REQUEST_TIME_FLOAT'];
    }

    /**
     * @var array $timers Array with the started timers
     */
    protected array $timers = [];

    /**
     * Gets the microtime elapsed from the script's start until the function was called
     * @return float The execution time
     */
    public function getExecutionTime() : float
    {
        return round(microtime(true) - $this->start, 4);
    }

    /**
     * Starts a timer
     * @param string $name The name of the timer to start
     * @return static
     */
    public function start(string $name = 'timer') : static
    {
        $this->timers[$name] = microtime(true);

        return $this;
    }

    /**
     * Ends a timer
     * @param string $name The name of the timer to end
     * @param bool $erase If true, will erase the timer
     * @return int Returns the time difference between the start and the end of the specified timer
     */
    public function stop(string $name = 'timer', bool $erase = true) : float
    {
        if (!isset($this->timers[$name])) {
            return 0;
        }

        $diff = round(microtime(true) - $this->timers[$name], 4);

        if ($erase) {
            unset($this->timers[$name]);
        }

        return $diff;
    }
}
