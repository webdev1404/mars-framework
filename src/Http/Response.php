<?php
/**
* The Http Response Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;
use Mars\App\InstanceTrait;
use CurlHandle;

/**
 * The Http Response Class
 * Encapsulates a http response
 */
class Response implements \Stringable
{
    use InstanceTrait;

    /**
     * @var int $code The http code of the response
     */
    public int $code {
        get => curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    /**
     * @var array $headers Array listing the response headers
     */
    public protected(set) array $headers {
        get {
            if (isset($this->headers)) {
                return $this->headers;
            }

            if (!isset($this->info['request_header'])) {
                $this->headers = [];
                return $this->headers;
            }
    
            if (!is_array($this->info['request_header'])) {
                $this->headers = explode("\n", $this->info['request_header']);
                return $this->headers;
            }
    
            $this->headers = $this->info['request_header'];

            return $this->headers;
        }
    }

    /**
     * @var string $body The body
     */
    public string $body {
        get => is_bool($this->result) ? '' : $this->result;
    }

    /**
     * @var int $error_no The generated error number, if any
     */
    public string $error_no {
        get => $this->result === false ? curl_errno($this->ch) : '';
    }

    /**
     * @var string $error The generated error, if any
     */
    public string $error {
        get => $this->result === false ? curl_error($this->ch) : '';
    }

    /**
     * @var array $info The request info
     */
    public protected(set) array $info {
        get {
            if (isset($this->info)) {
                return $this->info;
            }

            $this->info = curl_getinfo($this->ch);

            return $this->info;
        }
    }

    /**
     * @var bool|string $result The result of the request
     */
    protected bool|string $result;

    /**
     * @var CurlHandle $ch The curl handle
     */
    protected CurlHandle $ch;

    /**
     * Builds the curl result object
     * @param resource $ch The curl chandle
     * @param string|bool $result The curl result
     * @param App $app The app object
     */
    public function __construct(CurlHandle $ch, string|bool $result, App $app)
    {
        $this->app = $app;
        $this->ch = $ch;
        $this->result = $result;
    }

    /**
     * Returns the body of the request
     * @return string
     */
    public function __toString() : string
    {
        return $this->body;
    }

    /**
     * Returns true if the curl request was successful
     * @return bool
     */
    public function ok() : bool
    {
        if ($this->error || $this->code != 200) {
            return false;
        }

        return true;
    }

    /**
     * Returns the generated response
     * @return string
     */
    public function get() : string
    {
        return $this->body;
    }

    /**
     * Returns the generated response as json code
     * @return mixed
     */
    public function getJson() : mixed
    {
        return $this->app->json->decode($this->body);
    }
}
