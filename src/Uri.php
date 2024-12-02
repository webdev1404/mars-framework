<?php
/**
* The Uri Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;

/**
 * The Uri Class
 * Functionality for building & handling urls
 */
class Uri
{
    use InstanceTrait;

    /**
     * Determines if $url is a valid url
     * @param string $url The url
     * @return bool
     */
    public function isUrl(string $url) : bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Determines if $url is a local url
     * @param string $url The url
     * @return bool True if the url is local
     */
    public function isLocal(string $url) : bool
    {
        if (!str_starts_with(trim($url), $this->app->base_url)) {
            return false;
        }

        return true;
    }

    /**
     * Returns a filename from a local url
     * @param string $url The url
     * @return string The filename
     */
    public function getFromLocalUrl(string $url) : string
    {
        if (!$this->isLocal($url)) {
            return '';
        }

        $site_url = $this->removeScheme($this->app->base_url);
        $url = $this->removeScheme($url);

        $filename = $this->app->base_path . '/' . str_replace($site_url, '', $url);

        $this->app->file->checkFilename($filename);

        return $filename;
    }

    /**
     * Returns the root part of a url. It returns the scheme and the host
     * @param string $url The url
     * @return string The root
     */
    public function getRoot(string $url) : string
    {
        $scheme = (string)parse_url($url, PHP_URL_SCHEME);
        if ($scheme) {
            $scheme.= '://';
        }

        $host = (string)parse_url($url, PHP_URL_HOST);

        return $scheme . $host;
    }

    /**
     * Retrieves the host from a given URL
     * @param string $url The url
     * @return string The host
     */
    public function getHost(string $url) : string
    {
        return (string)parse_url($url, PHP_URL_HOST);
    }

    /**
     * Retrieves the domain from a given URL
     * @param string $url The url
     * @return string The domain
     */
    public function getDomain(string $url) : string
    {
        $host = $this->getHost($url);

        $parts = explode('.', $host);
        $count = count($parts);

        if ($count < 2) {
            return $host;
        }

        return $parts[$count - 2] . '.' . $parts[$count - 1];
    }

    /**
     * Retrieves the subdomain from a given URL
     * @param string $url The url
     * @return string The subdomain
     */
    public function getSubDomain(string $url) : string
    {
        $host = $this->getHost($url);

        $domain = $this->getDomain($url);

        $subdomain = str_replace($domain, '', $host);
        
        return trim($subdomain, '.');
    }

    /**
     * Returns the path, without the query, for a given url
     * @param string $url The url
     * @return string The path
     */
    public function getPath(string $url) : string
    {
        $path = (string)parse_url($url, PHP_URL_PATH);

        return rtrim($path, '/');
    }

    /**
     * Removes the query string from url, if exists
     * @param string $url The url
     * @return string The url with the querey string removed
     */
    public function getWithoutQuery(string $url) : string
    {
        $pos = strpos($url, '?');
        if ($pos === false) {
            return $url;
        }

        return strstr($url, '?', true);
    }

    /**
     * Determines if $param exists as a query param in $url
     * @param string $url The url to search for param\
     * @param string $param The param's name
     * @return bool True if exists, false otherwise
     */
    public function isInQuery(string $url, string $param) : bool
    {
        $pos = strpos($url, '?');
        if ($pos === false) {
            return false;
        }

        $query_str = substr($url, $pos + 1);

        parse_str($query_str, $params);

        return isset($params[$param]);
    }

    /**
     * Builds an url appending $params to $url
     * @param string $base_url The url to which params will be appended.
     * @param array $params Array containing the values to be appended. Specified as $name=>$value
     * @param bool $remove_empty_params If true, will remove from the query params the params with value = ''
     * @return string Returns the built url
     */
    public function build(string $base_url, array $params, bool $remove_empty_params = true) : string
    {
        if (!$params) {
            return $base_url;
        }

        $separator = '?';
        if (str_contains($base_url, '?')) {
            $separator = '&';
        }

        if ($remove_empty_params) {
            $params = array_filter($params);
        }

        $query_string = http_build_query($params);

        return $base_url . $separator . $query_string;
    }

    /**
     * Builds an url by appendding the $parts to $base_url
     * @param string $base_url The base url
     * @param array $parts Array with the parts to append to base_url
     * @return string Returns the $url
     */
    public function buildPath(string $base_url, array $parts) : string
    {
        $path_parts = [];
        foreach ($parts as $part) {
            $path_parts[] = rawurlencode($part);
        }

        return $base_url . '/' . implode('/', $path_parts);
    }

    /**
     * Normalizes an url. It will add $base_url to $url if $url is not a valid url
     * @param string $url The url
     * @param string $base_url The base url
     * @return string The normalized url
     */
    public function normalizeUrl(string $url, string $base_url) : string
	{
		if (!$this->isUrl($url)) {
			return $base_url . '/' . ltrim($url, '/');
		}

		return $url;
	}

    /**
     * Adds http:// at the beginning of $url, if it isn't already there
     * @param string $url The url
     * @return string Returns the $url prefixed by http://
     */
    public function toHttp(string $url) : string
    {
        $url = trim($url);

        if (str_starts_with($url, 'http://')) {
            return $url;
        } elseif (str_starts_with($url, 'https://')) {
            return 'http://' . substr($url, 8);
        } else {
            return 'http://' . $url;
        }
    }

    /**
     * Adds https:// at the beginning of $url, if it isn't already there
     * @param string $url The url
     * @return string Returns the $url prefixed by https://
     */
    public function toHttps(string $url) : string
    {
        $url = trim($url);

        if (str_starts_with($url, 'https://')) {
            return $url;
        } elseif (str_starts_with($url, 'http://')) {
            return 'https://' . substr($url, 7);
        } else {
            return 'https://' . $url;
        }
    }

    /**
     * Adds the scheme to an url
     * @param string $url The url
     * @param string $scheme The scheme to add. http or https. If empty, it will be determined based on the current document url
     * @return string The url
     */
    public function addScheme(string $url, string $scheme = '') : string
    {
        if (str_starts_with($url, 'https://') || str_starts_with($url, 'http://')) {
            return $url;
        }

        if (!$scheme) {
            $scheme = $this->app->scheme;
        } else {
            if (!str_contains($scheme, '://')) {
                $scheme.= '://';
            }
        }

        return $scheme . $url;
    }

    /**
     * Remove the scheme [http or https] from an url. https://google.com -> google.com
     * @param string $url The url
     * @return string The url without the scheme
     */
    public function removeScheme(string $url) : string
    {
        if (str_starts_with($url, 'https://')) {
            return substr($url, 8);
        } elseif (str_starts_with($url, 'http://')) {
            return substr($url, 7);
        }

        return $url;
    }

    /**
     * Returns javascript:void(0)
     * @return string
     */
    public function getEmpty() : string
    {
        return 'javascript:void(0)';
    }

    /**
     * Adds the ajax param to an url
     * @param string $base_url The url to the params will be appended
     * @param string $response_param The response param. Defaults to 'response'
     * @return string Returns the $url
     */
    public function addAjax(string $base_url, string $response_param = '') : string
    {
        if (!$response_param) {
            $response_param = 'response';
        }

        return $this->build($base_url, [$response_param  => 'ajax']);
    }
}
