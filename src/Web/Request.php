<?php
/**
* The Web Http Request Class
* @package Mars
*/

namespace Mars\Web;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Web Http Request Class
 * Wrapper around the Curl library for http requests
 */
class Request
{
    use Kernel;

    /**
     * @var int $timeout The timeout, in seconds
     */
    public int $timeout = 30;

    /**
     * @var bool $follow_location Determines the value of CURLOPT_FOLLOWLOCATION
     */
    public bool $follow_location = true;

    /**
     * @var bool $show_headers If true, the headers will be also returned
     */
    public bool $show_headers = false;

    /**
     * @var bool $verify_ssl If true, the ssl certificate will be verified
     */
    public bool $verify_ssl = true;

    /**
     * @var string $useragent The useragent used when making requests
     */
    public string $useragent {
        get => $this->app->useragent;
    }

    /**
     * @var array $options Array listing CURL options, if any
     */
    public array $options {
        get {
            if (isset($this->options)) {
                return $this->options;
            }

            $this->options = $this->app->config->curl->options;

            return $this->options;
        }
    }

    /**
     * Resets the options
     */
    public function resetOptions() : static
    {
        $this->options = $this->app->config->curl->options;

        return $this;
    }

    /**
     * Sets the basic curl options [header/useragent/followlocation]
     * @param string $url The url to fetch
     * @param array $options Curl options, if any
     * @return resource The curl handle
     */
    protected function init(string $url, array $options = [])
    {
        $options = $options + $this->options;

        $headers = $options['headers'] ?? [];
        $referer = $options['referer'] ?? '';
        $cookie_file = $options['cookie_file'] ?? '';
        $follow_location = $options['follow_location'] ?? $this->follow_location;
        $show_headers = $options['show_headers'] ?? $this->show_headers;
        $useragent = $options['useragent'] ?? $this->useragent;
        $timeout = $options['timeout'] ?? $this->timeout;
        $verify_ssl = $options['verify_ssl'] ?? $this->verify_ssl;
        $custom_request = $options['custom_request'] ?? '';

        unset($options['headers'], $options['referer'], $options['cookie_file'], $options['follow_location'], $options['show_headers'],
            $options['useragent'], $options['timeout'], $options['custom_request'], $options['verify_ssl']);

        $ch = curl_init();

        $curl_options = [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => $follow_location,
            CURLOPT_HEADER => $show_headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_TIMEOUT => $timeout
        ];

        if ($headers) {
            $curl_options[CURLOPT_HTTPHEADER] = $headers;
        }
        if ($referer) {
            $curl_options[CURLOPT_REFERER] = $referer;
        }
        if ($cookie_file) {
            $curl_options[CURLOPT_COOKIEFILE] = $cookie_file;
            $curl_options[CURLOPT_COOKIEJAR] = $cookie_file;
        }
        if ($useragent) {
            $curl_options[CURLOPT_USERAGENT] = $useragent;
        }
        if ($custom_request) {
            $curl_options[CURLOPT_CUSTOMREQUEST] = $custom_request;
        }

        if (!$verify_ssl) {
            $curl_options[CURLOPT_SSL_VERIFYPEER] = false;
            $curl_options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        $options = $options + $curl_options;
        curl_setopt_array($ch, $options);

        return $ch;
    }

    /**
     * Executes the curl session and returns the result
     * @param \CurlHandle $ch The curl handler
     * @return Response The response
     * @throws Exception if an error occurs
     */
    protected function exec($ch) : Response
    {
        $result = curl_exec($ch);

        return new Response($ch, $result, $this->app);
    }

    /**
     * Fetches an url with a custom request
     * @param string $url The url to fetch
     * @param string $request The custom request
     * @param array $options Curl options, if any
     * @return Response The response
     */
    public function custom(string $url, string $request, array $options = []) : Response
    {
        $options['custom_request'] = $request;

        $ch = $this->init($url, $options);

        return $this->exec($ch);
    }

    /**
     * Fetches an url with a GET request
     * @param string $url The url to fetch
     * @param array $options Curl options, if any
     * @return Response The response
     */
    public function get(string $url, array $options = []) : Response
    {
        $ch = $this->init($url, $options);

        return $this->exec($ch);
    }

    /**
     * Fetches an url with a POST request
     * @param string $url The url to fetch
     * @param array $data Array with the data to post
     * @param array $options Curl options, if any
     * @param array $files Files to send in the name=>filename format
     * @return Response The response
     */
    public function post(string $url, array $data, array $options = [], array $files = []) : Response
    {
        if ($files) {
            foreach ($files as $name => $filename) {
                $file = new \CURLFile($filename, null, basename($filename));
                $data[$name] = $file;
            }
        }

        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $data;

        $ch = $this->init($url, $options);

        return $this->exec($ch);
    }

    /**
     * Downloads a file with a get request
     * @param string $url The url to fetch
     * @param string $filename The local filename under which the file will be stored
     * @param array $options Curl options, if any
     * @param bool $download_if_exists If false, the file won't be downloaded, if it already exists
     * @return Response The response. If the file exists and $download_if_exists = false, it will return true
     * @throws Exception if the file can't be written
     */
    public function getFile(string $url, string $filename, array $options = [], bool $download_if_exists = true) : bool|Response
    {
        if (!$download_if_exists) {
            if (is_file($filename)) {
                return true;
            }
        }

        $f = fopen($filename, 'wb');
        if (!$f) {
            throw new \Exception(App::__('file_error_write', ['{FILE}' => $filename]));
        }

        //CURLOPT_RETURNTRANSFER must be set before CURLOPT_FILE. php bug?
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_FILE] = $f;

        $ch = $this->init($url, $options);

        $response = $this->exec($ch);

        fclose($f);

        return $response;
    }

    /**
     * Returns the contents of the file. If the file exists, it returns its content. If the file doesn't exist, it will download it with a get request
     * @param string $url The url to fetch
     * @param string $filename The local filename under which the file will be stored
     * @param array $options Curl options, if any
     * @return string The file's content
     * @throws \Exception If the file can't be written or read
     */
    public function getFileContent(string $url, string $filename, array $options = []) : string
    {
        if (!is_file($filename)) {
            $this->getFile($url, $filename, $options);
        }

        return file_get_contents($filename);
    }
}
