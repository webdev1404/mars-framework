<?php
/**
* The Page Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App;
use Mars\Helpers\Minifier;

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
     * @var bool $minify True, if the output can be minified
     */
    protected bool $minify {
        get => $this->app->config->cache_page_minify;
    }

    /**
     * @var int $expires_hours The interval - in hours - after which the content should be refreshed by the browser
     */
    protected int $expires_hours {
        get => $this->app->config->cache_page_expire_hours;
    }

    /**
     * @var string $path The folder where the content will be cached
     */
    protected string $path {
        get => $this->app->cache_path . '/pages';
    }

    /**
     * @var string $file The name of the file used to cache the content
     */
    public string $file {
        get {
            if (isset($this->file)) {
                return $this->file;
            }

            $this->file = $this->app->url_full;

            return $this->file;
        }
    }

    /**
     * @var string $extension The extension of the cache file
     */
    protected string $extension = 'htm';

    /**
     * @var string $driver_name The name of the driver to use
     */
    protected string $driver_name {
        get => $this->app->config->cache_page_driver;
    }

    /**
     * Builds the page cache object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if (!$this->app->is_web || !$this->app->config->cache_page_enable || defined('DISABLE_CACHE_PAGE')) {
            //return;
        }
        if ($this->app->config->debug || $this->app->config->development) {
            //return;
        }
        if ($this->app->request->method != 'get') {
            //return;
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

        $this->driver->store($this->filename, $content);

        return $this;
    }

    /**
     * Deletes the cache file
     * @return static
     */
    public function delete() : static
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
        $filename = $this->path . '/' . $this->getFile($file);

        $this->driver->delete($filename);

        return $this;
    }

    /**
     * Clears all the cached pages
     */
    public function clear() : static
    {
        $dir = $this->path;

        $this->app->dir->clean($dir);

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
        }
    }

    /**
     * Sends the 304 Not Modified headers, if the etag matches
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
     * Outputs the headers needed when outputing from the cache
     * @param int $last_modified The date when the cached file has been last modified
     * @param string $etag The etag
     */
    protected function outputHeaders(int $last_modified, string $etag)
    {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
        header('Etag: ' . $etag);
        header('Vary: Accept-Encoding');

        if ($this->expires_hours) {
            $seconds = $this->expires_hours * 3600;
            $expires = gmdate('D, d M Y H:i:s', time() + $seconds);

            header('Expires: ' . $expires . ' GMT');
            header('Cache-Control: max-age = ' . $seconds);
        } else {
            header('Cache-Control: public');
        }
    }

    /**
     * Outputs the cached content
     */
    protected function outputContent()
    {
        $this->outputContentType();

        $content = $this->driver->get($this->filename);
        
        header('Content-Length: ' . strlen($content));

        echo $content;
        die;
    }

    /**
     * Outputs the content type. Must be implemented by the classes extending Cachable
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
        return md5($this->file . $last_modified);
    }

    /**
     * Html minifies the content
     * @param string $content The code to minify
     * @return string The minified code
     */
    protected function minify(string $content) : string
    {
        $minifier = new Minifier(($this->app));
        return $minifier->minifyHtml($content);
    }
}
