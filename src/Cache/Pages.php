<?php
/**
* The Page Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App;
use Mars\Assets\Minifier;

/**
 * The Page Cache Class
 * Class which handles the caching of pages
 */
class Pages extends Cacheable
{
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
    protected string $driver_name {
        get {
            if (isset($this->driver_name)) {
                return $this->driver_name;
            }

            $this->driver_name = $this->app->config->cache->page->driver ?? ($this->app->config->cache->driver == 'memcache' ? 'memcache' : 'text');

            return $this->driver_name;
        }
    }

    /**
     * @see Cacheable::$driver_params
     * {@inheritDoc}
     */
    protected array $driver_params = [
        false,               // use files cache
        'cacheable_pages',   // driver type
    ];

    /**
     * @see Cache::$dir
     * {@inheritDoc}
     */
    public protected(set) string $dir = 'pages';

    /**
     * @see Cacheable::$can_hash
     * {@inheritDoc}
     */
    protected bool $can_hash = true;

    /**
     * @var bool $minify True, if the output can be minified
     */
    protected bool $minify {
        get => $this->app->config->cache->page->minify;
    }

    /**
     * @var string $file The name of the file used to cache the content
     */
    public string $file {
        get {
            if (isset($this->file)) {
                return $this->file;
            }

            $type = $this->app->request->is_json ? 'json' : 'html';

            $this->file = $this->app->url->full . '-' . $this->app->lang->code . '-' . $type;

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
     * @var bool $output_headers_on_store True if the headers should be outputted when storing the content in the cache.
     */
    protected bool $output_headers_on_store = false;

    /**
     * Builds the page cache object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if ($this->app->is_cli || !$this->app->config->cache->page->enable || defined('DISABLE_CACHE_PAGE')) {
            return;
        }
        if ($this->app->config->debug->enable || $this->app->config->development->enable) {
            return;
        }
        if ($this->app->request->method != 'get') {
            return;
        }

        $this->can_cache = true;
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
        if ($this->minify) {
            $content = $this->minify($content);
        }

        $this->driver->set($this->filename, $content, false);

        if ($this->output_headers_on_store) {
            $this->outputHeadersOnStore();
        }

        return $this;
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
     * Cleans the pages cache
     */
    public function clean() : static
    {
        $this->driver->clean($this->path);
        
        return $this;
    }

    /**
     * Outputs the content, if it's cached
     */
    public function output()
    {
        if (!$this->can_cache) {
            return;
        }

        $last_modified = $this->getLastModified();

        if ($last_modified) {
            //we have the content in the cache
            $etag = $this->getEtag($last_modified);

            //check if we can send the 304 Not Modified header
            $this->outputNotModified($last_modified, $etag);

            //output the cache headers
            $this->outputHeaders($last_modified, $etag);

            $this->outputContent();
        } else {
            $this->output_headers_on_store = true;
        }
    }

    /**
     * Sends the 304 Not Modified headers, if the etag matches or the content has not been modified since the date sent by the browser
     * @param int $last_modified The date when the cached file has been last modified
     * @param string $etag The etag
     */
    protected function outputNotModified(int $last_modified, string $etag)
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
    protected function outputHeaders(int $last_modified, string $etag)
    {
        header('Cache-Control: no-cache');
        header('Vary: Accept');
        header('Etag: "' . $etag . '"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
    }

    /**
     * Outputs the headers needed when storing the content in the cache
     */
    protected function outputHeadersOnStore()
    {
        if (headers_sent()) {
            return;
        }

        $last_modified = $this->getLastModified();

        if ($last_modified) {
            $etag = $this->getEtag($last_modified);

            $this->outputHeaders($last_modified, $etag);
        }
    }

    /**
     * Outputs the cached content
     */
    protected function outputContent()
    {
        $this->outputContentType();

        $content = $this->driver->get($this->filename, false);
        
        header('Content-Length: ' . strlen($content));

        echo $content;
        die;
    }

    /**
     * Outputs the content type. Must be implemented by the classes extending Cacheable
     * @return static
     */
    protected function outputContentType() : static
    {
        return $this;
    }

    /**
     * Returns the date when the cached file has been last modified
     * @return int
     */
    protected function getLastModified() : int
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

    /**
     * Html minifies the content
     * @param string $content The code to minify
     * @return string The minified code
     */
    protected function minify(string $content) : string
    {
        $minifier = new Minifier($this->app);
        return $minifier->minifyHtml($content);
    }
}
