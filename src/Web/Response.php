<?php
/**
* The Http Response Class
* @package Mars
*/

namespace Mars\Web;

use CurlHandle;
use Mars\App;
use Mars\App\Kernel;

/**
 * The Http Response Class
 * Encapsulates a http response
 */
class Response implements \Stringable
{
    use Kernel;

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
     * @var ?string $body The body. If the request failed, it will be null
     */
    public ?string $body {
        get => $this->result === false ? null : $this->result;
    }

    /**
     * @var int $error_no The generated error number, if any
     */
    public int $error_no {
        get => $this->result === false ? curl_errno($this->ch) : 0;
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
     * @param CurlHandle $ch The curl handle
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
        return $this->body ?? '';
    }

    /**
     * Returns true if the curl request was successful
     * @return bool
     */
    public function ok() : bool
    {
        if (!$this->error && $this->code >= 200 && $this->code < 300) {
            return true;
        }

        return false;
    }

    /**
     * Returns the generated response
     * @return ?string The response body or null if the request failed
     */
    public function get() : ?string
    {
        if (!$this->ok()) {
            return null;
        }

        return $this->body;
    }

    /**
     * Returns the generated response as json code
     * @return mixed The json decoded response or null if the request failed
     */
    public function getJson() : mixed
    {
        if (!$this->ok() || $this->body === null) {
            return null;
        }

        return $this->app->json->decode($this->body);
    }
}
