<?php
/**
* The Http Response Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;
use Mars\App\InstanceTrait;

/**
 * The Http Response Class
 * Encapsulates a http response
 */
class Response
{
    use InstanceTrait;

    /**
     * @var string $body The body
     */
    public string $body = '';

    /**
     * @var array $headers Array listing the response headers
     */
    public array $headers = [];

    /**
     * @var int $code The http code of the response
     */
    public int $code = 0;

    /**
     * @var int $error_no The generated error number, if any
     */
    public string $error_no = '';

    /**
     * @var string $error The generated error, if any
     */
    public string $error = '';

    /**
     * @var array $info The request info
     */
    protected array $info = [];

    /**
     * Builds the curl result object
     * @param resource $ch The curl chandle
     * @param string|bool $result The curl result
     * @param App $app The app object
     */
    public function __construct($ch, string|bool $result, App $app)
    {
        if ($result === false) {
            $this->error_no = curl_errno($ch);
            $this->error = curl_error($ch);
        }

        $this->app = $app;
        $this->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->info = curl_getinfo($ch);
        $this->headers = $this->buildHeaders();
        $this->body = is_bool($result) ? '' : $result;
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
     * Returns the body of the request
     * @return string
     */
    public function __toString() : string
    {
        return $this->getBody();
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
     * Alias for get()
     */
    public function getBody() : string
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

    /**
     * Get the HTTP response code.
     * @return int The HTTP response code.
     */
    public function getCode() : int
    {
        return $this->code;
    }

    /**
     * Retrieves the error message.
     * @return string The error message.
     */
    public function getError() : string
    {
        return $this->error;
    }

    /**
     * Retrieves the headers from the HTTP response.
     * @return array An array of headers.
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Returns the request headers
     * @return array
     */
    protected function buildHeaders() : array
    {
        if (!isset($this->info['request_header'])) {
            return [];
        }

        if (!is_array($this->info['request_header'])) {
            return explode("\n", $this->info['request_header']);
        }

        return $this->info['request_header'];
    }
}
