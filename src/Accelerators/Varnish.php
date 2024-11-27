<?php
/**
* The Varnish Accelerator Class
* @package Mars
*/

namespace Mars\Accelerators;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Http\Request;

/**
 * The Varnish Accelerator Class
 */
class Varnish implements DriverInterface
{
    use InstanceTrait;

    /**
     * @see \Mars\Accelerators\DriverInterface::delete()
     * {@inheritdoc}
     */
    public function delete(string $url) : bool
    {
        $response = $this->app->http->request->custom($this->app->base_url, 'PURGE');

        return $response->ok();
    }

    /**
     * @see \Mars\Accelerators\DriverInterface::deleteByPattern()
     * {@inheritdoc}
     */
    public function deleteByPattern(string $pattern) : bool
    {
        $response = $this->app->http->request->custom($this->app->base_url, 'BAN', ['headers' => ['X-Ban-Pattern: ' . $pattern]]);

        return $response->ok();
    }

    /**
     * @see \Mars\Accelerators\DriverInterface::deleteAll()
     * {@inheritdoc}
     */
    public function deleteAll() : bool
    {
        $response = $this->app->http->request->custom($this->app->base_url, 'FULLBAN');

        return $response->ok();
    }
}
