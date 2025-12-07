<?php
/**
* The Php Session Class
* @package Mars
*/

namespace Mars\Session;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Php Session Class
 * Session driver which uses the default php implementation
 */
class Php implements SessionInterface
{
    use Kernel;

    /**
     * Builds the Php Session driver
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if ($this->app->config->session->save_path) {
            session_save_path($this->app->config->session->save_path);
        }

        if ($this->app->config->session->cookie->path || $this->app->config->session->cookie->domain) {
            session_set_cookie_params(0, $this->app->config->session->cookie->path, $this->app->config->session->cookie->domain);
        }

        if ($this->app->config->session->name) {
            session_name($this->app->config->session->name);
        }
    }
}
