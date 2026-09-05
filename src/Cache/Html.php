<?php
/**
* The HTML Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Cache\Html\Headers;

/**
 * The HTML Cache Class
 * Class which handles the caching of HTML pages
 */
class Html extends Cacheable
{
    use LazyLoad;

    /**
     * @var bool $can_cache True if the content can be cached
     */
    public bool $can_cache = false;

    /**
     * @see Cacheable::$drivers_enabled
     * {@inheritDoc}
     */
    public protected(set) array $drivers_enabled = ['text', 'memcache'];

    /**
     * @see Cacheable::$driver_name
     * {@inheritDoc}
     */
    public string $driver_name {
        get => $this->app->config->cache->html->driver;
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        false,               // use files cache
        'cacheable_html',   // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'html';

    /**
     * @see Cacheable::$can_hash
     * {@inheritDoc}
     */
    protected bool $can_hash = true;

    /**
     * @var string $file The name of the file used to cache the content
     */
    public string $file {
        get {
            if (isset($this->file)) {
                return $this->file;
            }

            $this->file = $this->app->url->full . '-' . $this->app->lang->code;

            return $this->file;
        }
    }
    
    /**
     * @var string $filename The filename of the file used to cache the content
     */
    protected string $filename {
        get {
            if (isset($this->filename)) {
                return $this->filename;
            }

            $this->filename = $this->path . '/' . $this->getName($this->file);

            return $this->filename;
        }
    }

    /**
     * @var string $compression The compression method to use for the cached content. Eg: gzip, brotli, zstd
     */
    protected string $compression {
        get {
            if (isset($this->compression)) {
                return $this->compression;
            }

            $this->compression = '';

            if ($this->app->config->cache->html->compression->enable) {
                $encodings = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
                if ($encodings) {
                    $encodings = explode(',', strtolower($encodings));
                    $encodings = array_map('trim', $encodings);

                    foreach ($this->app->config->cache->html->compression->drivers as $driver) {
                        if (!isset($this->content_encoding[$driver])) {
                            continue;
                        }
                        if (in_array($driver, $encodings)) {
                            $this->compression = $driver;
                            break;
                        }
                    }
                }
            }

            return $this->compression;
        }
    }

    /**
     * @see Cacheable::$extension
     * {@inheritDoc}
     */
    public string $extension {
        get {
            if (isset($this->extension)) {
                return $this->extension;
            }

            $this->extension = $this->app->request->is_json ? 'json' : 'html';
            if (isset($this->extensions[$this->compression])) {
                $this->extension .= '.' . $this->extensions[$this->compression];
            }

            return $this->extension;
        }
    }

    /**
     * @var array $extensions The available compression extensions
     */
    protected array $extensions = [
        'gzip' => 'gz',
        'brotli' => 'br',
        'zstd' => 'zst',
    ];

    /**
     * @var array $content_encoding The content encoding types associated with the compression methods
     */
    protected array $content_encoding = [
        'gzip' => 'gzip',
        'brotli' => 'br',
        'zstd' => 'zstd',
    ];

    /**
     * @var bool $send_headers_on_store True if the headers should be sent when storing the content in the cache.
     */
    protected bool $send_headers_on_store = false;

    /**
     * @var Headers $headers The headers object
     */
    #[LazyLoadProperty]
    protected Headers $headers;

    /**
     * Builds the html cache object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if ($this->app->is_cli || !$this->app->config->cache->html->enable || defined('DISABLE_CACHE_HTML')) {
            return;
        }
        if ($this->app->config->debug->enable || $this->app->config->development->enable) {
            return;
        }
        if ($this->app->request->method != 'get') {
            return;
        }

        $this->can_cache = true;

        $this->lazyLoad($app);
    }

    /**
     * Stores the content in the cache
     * @param string $content The content to store
     * @return static
     */
    public function store(string $content) : static
    {
        if (!$this->can_cache) {
            return $this;
        }

        $this->storeHeaders();
        $this->storeContent($content);

        if ($this->send_headers_on_store) {
            $this->sendHeadersOnStore();
        }

        return $this;
    }

    /**
     * Stores the headers associated with the html content in the cache
     */
    protected function storeHeaders()
    {
        if (!$this->app->response->headers->list) {
            return;
        }

        $this->headers->set($this->file, $this->app->response->headers->list);
    }

    /**
     * Stores the html content in the cache
     * @param string $content The content to store
     */
    protected function storeContent(string $content)
    {
        if ($this->compression) {
            $content = $this->app->compression->compressWith($this->compression, $content, $this->app->config->cache->html->compression->level);
        }

        $this->driver->set($this->filename, $content);
    }

    /**
     * Deletes the cache file
     * @param string $name Unused parameter
     * @return static
     */
    public function delete(string $name = '') : static
    {
        $this->driver->delete($this->filename);

        return $this;
    }

    /**
     * Deletes a file from the cache
     * @param string $file The file name to delete
     * @return static
     */
    public function deleteFile(string $file) : static
    {
        $filename = $this->path . '/' . $this->getName($file);

        $this->driver->delete($filename);

        return $this;
    }

    /**
     * Cleans the html cache
     */
    public function clean() : static
    {
        $this->driver->clean($this->path);
        
        return $this;
    }

    /**
     * Sends the cached html content
     */
    public function send()
    {
        if (!$this->can_cache) {
            return;
        }

        $last_modified = $this->getLastModified();

        if ($last_modified) {
            //send the stored headers
            $this->sendStoredHeaders();

            //we have the content in the cache
            $etag = $this->getEtag($last_modified);

            //check if we can send the 304 Not Modified header
            $this->sendNotModified($last_modified, $etag);

            //output the cache headers
            $this->sendCacheHeaders($last_modified, $etag);

            $this->sendContent();
        } else {
            $this->send_headers_on_store = true;
        }
    }

    /**
     * Sends the headers stored in the cache
     */
    protected function sendStoredHeaders()
    {
        //send the stored headers
        $headers = $this->headers->get($this->file) ?? [];

        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
    }

    /**
     * Sends the 304 Not Modified headers, if the etag matches or the content has not been modified since the date sent by the browser
     * @param int $last_modified The date when the cached file has been last modified
     * @param string $etag The etag
     */
    protected function sendNotModified(int $last_modified, string $etag)
    {
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if ($_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
                header('HTTP/1.1 304 Not Modified');
                die;
            }
        }

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $cache_modified = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);

            if ($last_modified <= $cache_modified) {
                header('HTTP/1.1 304 Not Modified');
                die;
            }
        }
    }

    /**
     * Outputs the headers needed when outputting from the cache
     * @param int $last_modified The date when the cached file has been last modified
     * @param string $etag The etag
     */
    protected function sendCacheHeaders(int $last_modified, string $etag)
    {
        header('Cache-Control: no-cache');
        header('Vary: Accept');
        header('Etag: "' . $etag . '"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
    }

    /**
     * Outputs the headers needed when storing the content in the cache
     */
    protected function sendHeadersOnStore()
    {
        if (headers_sent()) {
            return;
        }

        $last_modified = $this->getLastModified();

        if ($last_modified) {
            $etag = $this->getEtag($last_modified);

            $this->sendCacheHeaders($last_modified, $etag);
        }
    }

    /**
     * Serves the cached content
     */
    protected function sendContent()
    {
        $size = $this->driver->getSize($this->filename);
        if ($size !== null) {
            header('Content-Length: ' . $size);
        }

        if ($this->compression) {
            header('Content-Encoding: ' . $this->content_encoding[$this->compression]);
        }

        $this->driver->output($this->filename);
        die;
    }

    /**
     * Returns the date when the cached file has been last modified
     * @return int|null The date when the cached file has been last modified or null if not found
     */
    protected function getLastModified() : ?int
    {
        return $this->driver->getLastModified($this->filename);
    }

    /**
     * Returns the etag of the cached file
     * @param int $last_modified The date when the cached file has been last modified
     * @return string The etag
     */
    protected function getEtag(int $last_modified) : string
    {
        return md5($this->filename . $last_modified);
    }
}
