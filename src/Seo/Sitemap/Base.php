<?php
/**
* The Base Sitemap Class
* @package Mars
*/

namespace Mars\Seo\Sitemap;

use Mars\App\Kernel;

/**
 * The base class for generating sitemap XML files.
 */
abstract class Base
{
    use Kernel;

    /**
     * Opens a file for writing and returns the file handle
     * @param string $filename The name of the file to open
     * @return The file handle
     * @throws \Exception If the file cannot be opened for writing
     */
    public function open(string $filename)
    {
        $f = fopen($filename, 'w');
        if (!$f) {
            throw new \Exception("Failed to open file {$filename} for writing.");
        }

        fwrite($f, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($f, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

        return $f;
    }

    /**
     * Closes the given file handle
     * @param $f The file handle to close
     */
    public function close($f)
    {
        fwrite($f, '</urlset>' . "\n");

        fclose($f);
    }

    /**
     * Writes a single URL entry to the sitemap XML
     * @param $f The file handle to write to
     * @param string $url The URL to include in the sitemap
     * @param string $last_modified The last modified date of the URL (optional)
     * @param string $change_frequency The change frequency of the URL (optional)
     * @param float $priority The priority of the URL (optional)
     */
    public function write($f, string $url, string $last_modified = '', string $change_frequency = '', float $priority = 0.5)
    {
        fwrite($f, '<url>' . "\n");
        fwrite($f, '    <loc>' . $this->app->escape->html($url) . '</loc>' . "\n");

        if ($last_modified) {
            fwrite($f, '    <lastmod>' . $this->app->escape->html($last_modified) . '</lastmod>' . "\n");
        }
        if ($change_frequency) {
            fwrite($f, '    <changefreq>' . $this->app->escape->html($change_frequency) . '</changefreq>' . "\n");
        }
            
        fwrite($f, '    <priority>' . $this->app->escape->html((string)$priority) . '</priority>' . "\n");

        fwrite($f, '</url>' . "\n");
    }
}
