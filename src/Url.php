<?php
/**
* The Url Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Url Class
 * Encapsulates methods for working with urls
 */
class Url implements \Stringable
{
    use Kernel;

    /**
     * @var string The url
     */
    public protected(set) string $url = '';

    /**
     * @var bool $is_valid True if the url is valid
     */
    public protected(set) bool $is_valid {
        get {
            if (isset($this->is_valid)) {
                return $this->is_valid;
            }

            $this->is_valid = (bool) filter_var($this->url, FILTER_VALIDATE_URL);

            return $this->is_valid;
        }
    }

    /**
     * @var bool $is_local True if the url is local
     */
    public protected(set) bool $is_local {
        get {
            if (isset($this->is_local)) {
                return $this->is_local;
            }

            $this->is_local = true;
            if (!str_starts_with(trim($this->url), $this->app->base_url)) {
                $this->is_local = false;
            }

            return $this->is_local;
        }
    }

    /**
     * @var string The scheme of the url. Will also contain '://'
     */
    public protected(set) string $scheme {
        get {
            if (isset($this->scheme)) {
                return $this->scheme;
            }

            $this->scheme = (string)parse_url($this->url, PHP_URL_SCHEME);
            if ($this->scheme) {
                $this->scheme .= '://';
            }

            return $this->scheme;
        }
    }

    /**
     * @var string The host of the url
     */
    public protected(set) string $host {
        get {
            if (isset($this->host)) {
                return $this->host;
            }

            $this->host = (string)parse_url($this->url, PHP_URL_HOST);

            return $this->host;
        }
    }

    /**
     * @var string The port of the url
     */
    public protected(set) string $port {
        get {
            if (isset($this->port)) {
                return $this->port;
            }

            $this->port = (string)parse_url($this->url, PHP_URL_PORT);

            return $this->port;
        }
    }

    /**
     * @var string The root of the url. It contains the scheme and the host
     */
    public protected(set) string $root {
        get {
            if (isset($this->root)) {
                return $this->root;
            }

            $this->root = $this->scheme . $this->host;
            if ($this->port) {
                $this->root .= ':' . $this->port;
            }

            return $this->root;
        }
    }

    /**
     * @var string $domain The domain of the url. Eg: mydomain.com. Use with care, it will not work with TLDs like .co.uk
     */
    public protected(set) string $domain {
        get {
            if (isset($this->domain)) {
                return $this->domain;
            }

            $parts = explode('.', $this->host);
            $count = count($parts);

            if ($count < 2) {
                $this->domain = $this->host;
            } else {
                $this->domain = $parts[$count - 2] . '.' . $parts[$count - 1];
            }

            return $this->domain;
        }
    }

    /**
     * @var string $subdomain The subdomain of the url. Eg: www
     */
    public protected(set) string $subdomain {
        get {
            if (isset($this->subdomain)) {
                return $this->subdomain;
            }

            $this->subdomain = str_replace($this->domain, '', $this->host);
            $this->subdomain = trim($this->subdomain, '.');

            return $this->subdomain;
        }
    }

    /**
     * @var string $path The path of the url. Eg: my/path
     */
    public protected(set) string $path {
        get {
            if (isset($this->path)) {
                return $this->path;
            }

            $this->path = (string)parse_url($this->url, PHP_URL_PATH);
            $this->path = ltrim($this->path, '/');

            return $this->path;
        }
    }

    /**
     * @var string $path_name The url without the query string. Eg: https://mydomain.com/my/path
     */
    public protected(set) string $path_name {
        get {
            if (isset($this->path_name)) {
                return $this->path_name;
            }

            $this->path_name = $this->root . '/' . $this->path;

            return $this->path_name;
        }
    }

    /**
     * @var string $query The query string of the url. Eg: id=5&name=test
     */
    public protected(set) string $query {
        get {
            if (isset($this->query)) {
                return $this->query;
            }

            $this->query = (string)parse_url($this->url, PHP_URL_QUERY);

            return $this->query;
        }
    }

    /**
     * @var string $fragment The fragment of the url. Eg: fragment
     */
    public protected(set) string $fragment {
        get {
            if (isset($this->fragment)) {
                return $this->fragment;
            }

            $this->fragment = (string)parse_url($this->url, PHP_URL_FRAGMENT);

            return $this->fragment;
        }
    }

    /**
     * Builds the Url object
     * @param string $url The url
     * @param bool $sanitize If true, it will sanitize the url
     */
    public function __construct(string $url, bool $sanitize = true)
    {
        $this->url = $url;

        if ($sanitize) {
            $this->url = filter_var($this->url, FILTER_SANITIZE_URL);
        }
    }

    /**
     * Returns the current url
     * @return string The current url
     */
    public function __toString() : string
    {
        return $this->url;
    }

    /**
     * Returns the local filename from a local url
     * !!!!Use with caution!!!!
     * @return string The local filename. If the url is not local, it will return an empty string
     * @throws \Exception If the file contains invalid characters or is not inside the open_basedir paths
     */
    public function getLocalFilename() : string
    {
        $filename = '';
        if ($this->is_local) {
            $filename = $this->app->base_path . '/' . $this->path;
            $filename = preg_replace('/[\/\\\]+/', '/', $filename);

            $this->app->file->check($filename);
        }

        return $filename;
    }

    /**
     * Determines if $param exists in the query string
     * @param string $param The param's name
     * @return bool True if exists, false otherwise
     */
    public function contains(string $param) : bool
    {
        if (!$this->query) {
            return false;
        }

        parse_str($this->query, $params);

        return isset($params[$param]);
    }

    /**
     * Builds an url by appending the path parts
     * @param string|array $parts Array with the parts to append
     * @param array $params Array with the query parameters
     * @param bool $encode If true, it will encode the parts using rawurlencode
     * @param bool $remove_empty_params If true, it will remove empty parameters
     * @return static Returns a new url instance
     */
    public function get(string|array $parts, array $params = [], bool $encode = true, bool $remove_empty_params = true) : static
    {
        $parts = array_filter($this->app->array->get($parts));
        if (!$parts && !$params) {
            return clone $this;
        }

        if ($encode) {
            $parts = array_map(fn ($part) => rawurlencode($part), $parts);
        }

        $url = new static($this->url . '/' . implode('/', $parts));
        if ($params) {
            return $url->add($params, $remove_empty_params);
        }

        return $url;
    }

    /**
     * Builds an url, by adding the params to the query string
     * @param array $params Array containing the values to be appended. Specified as name => value
     * @param bool $remove_empty_params If true, will not add empty_params
     * @return static Returns a new url instance
     */
    public function add(array $params, bool $remove_empty_params = true) : static
    {
        if ($remove_empty_params) {
            $params = array_filter($params);
        }

        if (!$params) {
            return clone $this;
        }

        $separator = '?';
        if (str_contains($this->url, '?')) {
            $separator = '&';
        }

        $query_string = http_build_query($params);

        return new static($this->url . $separator . $query_string);
    }

    /**
     * Normalizes an url. It will prepend the url with $base_url, if the url is not a valid url
     * @param string $base_url The base url
     * @return Url Returns a new url instance of the normalized url
     */
    public function normalize(string $base_url) : static
    {
        if ($this->is_valid) {
            return new static($this->url);
        }

        return new static(rtrim($base_url, '/') . '/' . ltrim($this->url, '/'));
    }
}
