<?php
/**
* The Varnish Accelerator Class
* @package Mars
*/

namespace Mars\Accelerators;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Varnish Accelerator Class
 */
class Varnish implements AcceleratorInterface
{
    use Kernel;

    /**
     * AcceleratorInterface::delete()
     * {@inheritdoc}
     */
    public function delete(string $url) : bool
    {
        $response = $this->app->web->request->custom($this->app->base_url, 'PURGE');

        return $response->ok();
    }

    /**
     * AcceleratorInterface::deleteByPattern()
     * {@inheritdoc}
     */
    public function deleteByPattern(string $pattern) : bool
    {
        $response = $this->app->web->request->custom($this->app->base_url, 'BAN', ['headers' => ['X-Ban-Pattern: ' . $pattern]]);

        return $response->ok();
    }

    /**
     * AcceleratorInterface::deleteAll()
     * {@inheritdoc}
     */
    public function deleteAll() : bool
    {
        $response = $this->app->web->request->custom($this->app->base_url, 'FULLBAN');

        return $response->ok();
    }
}
