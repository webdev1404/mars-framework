<?php
/**
* The Throttle Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Throttle Class
 * Implements rate limiting to throttle requests based on attempt counts
 */
class Throttle
{
    use Kernel;

    /**
     * @var bool $enabled Whether throttling is enabled
     */
    protected bool $enabled {
        get => $this->app->config->throttle->enable;
    }

    /**
     * @var array|null $attempts The attempts data
     */
    protected ?array $attempts = null;

    /**
     * Checks whether a key is blocked
     * @param string $key The key
     * @param int $max_attempts The maximum number of attempts allowed
     * @param int $duration The block duration in seconds
     * @return bool Whether the key is blocked
     */
    public function isBlocked(string $key, int $max_attempts, int $duration): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $this->attempts = $this->app->cache->get($this->getKey($key));
        if ($this->attempts === null) {
            return false;
        }

        if ($this->attempts['attempts'] >= $max_attempts) {
            $block_until = $this->attempts['last_attempt'] + $duration;

            if (time() < $block_until) {
                return true;
            } else {
                $this->attempts = null;
                $this->app->cache->delete($this->getKey($key));
            }
        }

        return false;
    }

    /**
     * Gets the cache key for a throttle key
     * @param string $key The key
     * @return string The cache key
     */
    protected function getKey(string $key): string
    {
        return $key . '-throttle';
    }

    /**
     * Records a hit for a key
     * @param string $key The key
     */
    public function hit(string $key)
    {
        if (!$this->enabled) {
            return;
        }

        $attempts = $this->attempts['attempts'] ?? 0;
        $attempts++;

        $data = [
            'attempts' => $attempts,
            'last_attempt' => time(),
        ];

        $this->app->cache->set($this->getKey($key), $data);
    }
}
