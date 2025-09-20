<?php
/**
* The Uri Class
* @package Mars
*/

namespace Mars\System;

use Mars\App\Kernel;
use Mars\Url;

/**
 * The Uri Class
 * Functionality for building & handling urls
 */
class Uri implements \Stringable
{
    use Kernel;

    /**
     * @var string $request_uri The request URI as read from $_SERVER['REQUEST_URI']
     */
    public protected(set) string $request_uri {
        get {
            if (isset($this->request_uri)) {
                return $this->request_uri;
            }

            $this->request_uri = new Url($_SERVER['REQUEST_URI'] ?? '')->path;

            return $this->request_uri;
        }
    }

    public protected(set) array $parts {
        get {
            if (isset($this->parts)) {
                return $this->parts;
            }

            $this->parts = explode('/', $this->request_uri);

            return $this->parts;
        }
    }

    /**
     * @var Url The url. Eg: http://mydomain.com/mars
     */
    public protected(set) Url $base {
        get {
            if (isset($this->base)) {
                return $this->base;
            }

            $this->base = new Url($this->app->config->url);

            return $this->base;
        }
    }

    /**
     * @var Url $current The current url. Does not include the query string
     */
    public protected(set) Url $current {
        get {
            if (isset($this->current)) {
                return $this->current;
            }

            $this->current = $this->build($this->app->base_url, [$this->request_uri], false);

            return $this->current;
        }
    }

    /**
     * @var string $path The path of the current url
     */
    public string $path {
        get => $this->current->path;
    }

    /**
     * @var string $root The root url. Includes the language code, if languages_multi is enabled. Eg: http://mydomain.com/mars/en
     */
    public protected(set) string $root {
        get {
            if (isset($this->root)) {
                return $this->root;
            }

            $this->root = $this->base;
            if ($this->app->lang->multi) {
                // Append the language code to the base URL, if we are using multi-languages
                $this->root .= '/' . $this->app->lang->code;
            }

            return $this->root;
        }
    }

    /**
     * @var string $lang The language code from the url, if multi languages is enabled. Eg: en, fr, de
     */
    public protected(set) string $lang {
        get {
            if (isset($this->lang)) {
                return $this->lang;
            }

            // If multi-language is enabled, the first part of the URL is the language code
            $this->lang = '';
            if ($this->app->lang->multi) {
                $this->lang = $this->parts[0] ?? '';
            }

            return $this->lang;
        }
    }

    /**
     * @var Url $full The full url. Includes the query string
     */
    public protected(set) Url $full {
        get {
            if (isset($this->full)) {
                return $this->full;
            }

            $query_string = $_SERVER['QUERY_STRING'] ?? '';
            if ($query_string) {
                $this->full = new Url($this->current . '?' . $query_string);
            } else {
                $this->full = $this->current;
            }

            return $this->full;
        }
    }

    /**
     * Returns the current url
     * @return string The current url
     */
    public function __toString() : string
    {
        return $this->current;
    }

    /**
     * Determines if $url is a valid url
     * @param string $url The url
     * @return bool
     */
    public function isValid(string $url) : bool
    {
        return new Url($url)->is_valid;
    }

    /**
     * Determines if $url is a local url
     * @param string $url The url
     * @return bool True if the url is local
     */
    public function isLocal(string $url) : bool
    {
        return new Url($url)->is_local;
    }

    /**
     * Returns the scheme of a given url
     * @param string $url The url
     * @return string The scheme
     */
    public function getScheme(string $url) : string
    {
        return new Url($url)->scheme;
    }

    /**
     * Returns the host of a given url
     * @param string $url The url
     * @return string The host
     */
    public function getHost(string $url) : string
    {
        return new Url($url)->host;
    }

    /**
     * Returns the port of a given url
     * @param string $url The url
     * @return string The port
     */
    public function getPort(string $url) : string
    {
        return new Url($url)->port;
    }

    /**
     * Returns the root of a given url. It contains the scheme, host and port
     * @param string $url The url
     * @return string The root
     */
    public function getRoot(string $url) : string
    {
        return new Url($url)->root;
    }

    /**
     * Returns the domain from a given URL
     * @param string $url The url
     * @return string The domain
     */
    public function getDomain(string $url) : string
    {
        return new Url($url)->domain;
    }

    /**
     * Returns the subdomain from a given URL
     * @param string $url The url
     * @return string The subdomain
     */
    public function getSubdomain(string $url) : string
    {
        return new Url($url)->subdomain;
    }

    /**
     * Returns the path of a given url
     * @param string $url The url
     * @return string The path
     */
    public function getPath(string $url) : string
    {
        return new Url($url)->path;
    }

    /**
     * Returns the path name of a given url, without the query string
     * @param string $url The url
     * @return string The path name
     */
    public function getPathName(string $url) : string
    {
        return new Url($url)->path_name;
    }

    /**
     * Returns the query string of a given url
     * @param string $url The url
     * @return string The query string
     */
    public function getQuery(string $url) : string
    {
        return new Url($url)->query;
    }

    /**
     * Returns the fragment of a given url
     * @param string $url The url
     * @return string The fragment
     */
    public function getFragment(string $url) : string
    {
        return new Url($url)->fragment;
    }

    /**
     * Returns the filename from a given url
     * !!!!Use with caution!!!!
     * @param string $url The url
     * @return string The local filename. If the url is not local, it will return an empty string
     */
    public function getLocalFilename(string $url) : string
    {
        return new Url($url)->getLocalFilename();
    }

    /**
     * Builds an url by appendding the $parts to $base_url
     * @param string $base_url The base url
     * @param array $parts Array with the parts to append to base_url
     * @param bool $encode If true, it will encode the parts using rawurlencode
     * @return Url Returns the url
     */
    public function build(string $base_url, array $parts, bool $encode = true) : Url
    {
        return new Url($base_url)->build($parts, $encode);
    }

    /**
     * Builds an url, by adding the params to the query string
     * @param string $base_url The base url
     * @param array $params Array containing the values to be appended. Specified as name = >value
     * @param bool $remove_empty_params If true, will not add empty_params
     * @return static Returns a new url instance
     */
    public function add(string $base_url, array $params, bool $remove_empty_params = true) : Url
    {
        return new Url($base_url)->add($params, $remove_empty_params);
    }

    /**
     * Adds the ajax param to an url
     * @param string $url The url
     * @param string $response_param The response param
     * @return Url Returns the url
     */
    public function addAjax(string $url, string $response_param = 'response') : Url
    {
        return new Url($url)->add([$response_param => 'ajax']);
    }

    /**
     * Determines if $param exists as a query param in $url
     * @param string $url The url
     * @param string $param The param's name
     * @return bool True if exists, false otherwise
     */
    public function contains(string $url, string $param) : bool
    {
        return new Url($url)->contains($param);
    }

    /**
     * Normalizes an url. It will prepend $url with $base_url, if the url is not a valid url
     * @param string $url The url
     * @param string $base_url The base url
     * @return Url The normalized url
     */
    public function normalize(string $url, string $base_url) : Url
    {
        return new Url($url)->normalize($base_url);
    }
}
