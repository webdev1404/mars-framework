<?php
/**
* The Http Request Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;

/**
 * The Http Request Class
 * Wrapper around the Curl library for http requests
 */
class Request
{
    use \Mars\AppTrait;

    /**
     * @var int $timeout The timeout, in seconds
     */
    public int $timeout = 30;

    /**
     * @var string $useragent The useragent used when making requests
     */
    public string $useragent = '';

    /**
     * @var bool $follow_location Determines the value of CURLOPT_FOLLOWLOCATION
     */
    public bool $follow_location = true;

    /**
     * @var bool $show_headers If true,the headers will be also returned
     */
    public bool $show_headers = false;

    /**
     * @var array $options Array listing CURL options, if any
     */
    public array $options = [];

    /**
     * Builds the Http Request object
     * @param App $app The app object
     */
    public function __construct(App $app = null)
    {
        $this->app = $app ?? $this->getApp();
        $this->useragent = $this->app->getUseragent();
        $this->options = $this->app->config->curl_options;
    }

    /**
     * @param array $options The curl options
     */
    public function setOptions(array $options) : static
    {
        $this->options = $options;

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
        $options = array_merge($this->options + $options);

        $headers = $options['headers'] ?? [];
        $referer = $options['referer'] ?? '';
        $cookie_file = $options['cookie_file'] ?? '';
        $follow_location = $options['follow_location'] ?? $this->follow_location;
        $show_headers = $options['show_headers'] ?? $this->show_headers;
        $useragent = $options['useragent'] ?? $this->useragent;
        $timeout = $options['timeout'] ?? $this->timeout;
        $custom_request = $options['custom_request'] ?? '';

        $ch = curl_init();

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        if ($cookie_file) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        }
        if ($useragent) {
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        }
        if ($custom_request) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom_request);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_location);
        curl_setopt($ch, CURLOPT_HEADER, $show_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt_array($ch, $options);

        return $ch;
    }

    /**
     * Executes the curl session and returns the result
     * @param \CurlHandle $ch The curl handler
     * @return Response The response
     */
    protected function exec($ch) : Response
    {
        $result = curl_exec($ch);

        $response = new Response($ch, $result, $this->app);

        curl_close($ch);

        return $response;
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
     * @param array $files Files to send in the name=>filename format
     * @param array $options Curl options, if any
     * @return Response The response
     */
    public function post(string $url, array $data, array $files = [], array $options = []) : Response
    {
        if ($files) {
            foreach ($files as $name => $filename) {
                $file = new \CURLFile($filename, null, basename($filename));
                $data[$name] = $file;
            }
        }

        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ];

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

        $options[CURLOPT_FILE] = $f;

        $ch = $this->init($url, $options);

        $response = $this->exec($ch);

        fclose($f);

        return $response;
    }

    /**
     * Returns the contents of the file. If the file exists, if returns it's content. If the file doesn't exist, it will download it with a get request
     * @param string $url The url to fetch
     * @param string $filename The local filename under which the file will be stored
     * @param array $options Curl options, if any
     * @return string The file's content
     * @throws Exception if the file can't be written
     */
    public function getFileContent(string $url, string $filename, array $options = []) : string
    {
        if (!is_file($filename)) {
            $this->getFile($url, $filename, $options);
        }

        return file_get_contents($filename);
    }
}
