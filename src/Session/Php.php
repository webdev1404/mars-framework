<?php
/**
* The Php Session Class
* @package Mars
*/

namespace Mars\Session;

/**
 * The Php Session Class
 * Session driver which uses the default php implementation
 */
class Php extends Base
{
    /**
     * @see SessionInterface::start()
     */
    public function start()
    {
        if ($this->app->config->session->save_path) {
            session_save_path($this->app->config->session->save_path);
        }

        if ($this->app->config->session->name) {
            session_name($this->app->config->session->name);
        }

        session_set_cookie_params([
            'lifetime' => $this->app->config->session->cookie->lifetime ?? 0,
            'path'     => $this->app->config->session->cookie->path,
            'domain'   => $this->app->config->session->cookie->domain,
            'secure'   => $this->app->config->session->cookie->secure,
            'httponly' => $this->app->config->session->cookie->httponly ?? true,
            'samesite' => $this->app->config->session->cookie->samesite,
        ]);

        session_start();
    }
}
