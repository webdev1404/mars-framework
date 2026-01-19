<?php
/**
* The Cookie Response Class
* @package Mars
*/

namespace Mars\Http\Response\Data;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Cookie Response Class
 * Handles the setting/removing cookies
 */
class Cookies
{
    use Kernel;

    /**
     * @var int $expires The cookie's expires timestamp
     */
    protected int $expires {
        get => time() + (3600 * 24 * $this->app->config->cookie->expire_days);
    }

    /**
     * @var string $path The cookie's path
     */
    protected string $path {
        get => $this->app->config->cookie->path;
    }

    /**
     * @var string $domain The cookie's domain
     */
    protected string $domain {
        get => $this->app->config->cookie->domain;
    }

    /**
     * @var bool $secure If true the cookies will only be sent over secure connections.
     */
    protected bool $secure {
        get => $this->app->config->cookie->secure;
    }

    /**
     * @var bool $httponly If true then httponly flag will be set for the cookies
     */
    public bool $httponly {
        get => $this->app->config->cookie->httponly;
    }

    /**
     * @var string $samesite The SameSite attribute for the cookie
     */
    public string $samesite {
        get => $this->app->config->cookie->samesite;
    }

    /**
     * Sets a cookie. The data is first encoded using json encode
     * @param string $name The name of the cookie
     * @param mixed $data The data to be written
     * @param int $expires The expiration date. If null, $this->expires is used
     * @param string $path The path in which the cookie is valid. If null, $this->path is used
     * @param string $domain The domain in which the cookie is valid. If null, $this->domain is used
     * @param bool $secure If true, the cookie will only be set over a https connection
     * @param bool $httponly If true the cookie is accessible only over http and not with javascript
     * @param string|null $samesite The SameSite attribute for the cookie
     * @param bool $encode If true will encode the data
     * @return static
     */
    public function set(string $name, $data, ?int $expires = null, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httponly = null, ?string $samesite = null, bool $encode = true) : static
    {
        if ($encode) {
            $data = $this->app->json->encode($data);
        }

        $expires = $expires ?? $this->expires;
        $path = $path ?? $this->path;
        $domain = $domain ?? $this->domain;
        $secure = $secure ?? $this->secure;
        $httponly = $httponly ?? $this->httponly;
        $samesite = $samesite ?? $this->samesite;

        $options = [
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];

        setcookie($name, $data, $options);

        return $this;
    }

    /**
     * Deletes a cookie
     * @param string $name The name of the cookie
     * @param string $path The path in which the cookie is valid. If null, $this->path is used
     * @param string $domain The domain in which the cookie is valid. If null, $this->domain is used
     * @param bool $secure If true, the cookie will only be set over a https connection
     * @param bool $httponly If true the cookie is accessible only over http and not with javascript
     * @param string|null $samesite The SameSite attribute for the cookie
     * @return static
     */
    public function unset(string $name, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httponly = null, ?string $samesite = null) : static
    {
        $path = $path ?? $this->path;
        $domain = $domain ?? $this->domain;
        $secure = $secure ?? $this->secure;
        $httponly = $httponly ?? $this->httponly;
        $samesite = $samesite ?? $this->samesite;

        $options = [
            'expires' => time() - 3600,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];

        setcookie($name, '', $options);

        return $this;
    }
}
