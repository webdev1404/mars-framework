<?php
/**
 * The Routes Sitemap Class
 * @package Mars
 */
namespace Mars\Seo\Sitemap;

/**
 * The Routes Sitemap Class.
 * Class is responsible for generating sitemap XML files for the application's routes.
 */
class Routes extends Base
{
    /**
     * Generates the sitemap for the routes
     * @return array The list of generated sitemap files
     */
    public function generate() : array
    {
        $routes = $this->app->router->loader->load();
        if (!$routes) {
            return [];
        }

        $hashes = $routes->hashes ?? [];
        $data = $routes->data ?? [];
        $urls = $hashes['get'] ?? [];
        if (!$urls) {
            return [];
        }

        $sitemaps = [];
        foreach ($urls as $code => $urls_list) {
            $sitemap = "routes-{$code}.xml";

            $this->addToSitemap($sitemap, $urls_list, $data);

            $sitemaps[] = $sitemap;
        }

        return $sitemaps;
    }

    /**
     * Adds a list of URLs to the sitemap file
     * @param string $sitemap The path to the sitemap file
     * @param array $urls_list The list of URLs to add
     * @param array $data The route data associated with the URLs
     */
    protected function addToSitemap(string $sitemap, array $urls_list, array $data)
    {
        $sitemap_filename = $this->app->cache->sitemap->getFilename($sitemap);
        $f = $this->open($sitemap_filename);

        foreach ($urls_list as $hashes) {
            foreach ($hashes as $hash => $data_key) {
                $route = $data[$data_key] ?? null;
                if (!$route) {
                    continue;
                }

                if (!$route['sitemap']) {
                    continue;
                }

                $type = $route['type'];
                if (!isset($this->app->config->sitemap->routes->types[$type])) {
                    continue;
                }

                $url = $this->getUrl($route);
                $last_modified = $this->getLastModified($route);
                $change_frequency = $this->app->config->sitemap->routes->types[$type]['change_frequency'] ?? $this->app->config->sitemap->change_frequency;
                $priority = $this->app->config->sitemap->routes->types[$type]['priority'] ?? $this->app->config->sitemap->priority;

                $this->write($f, $url, $last_modified, $change_frequency, $priority);
            }
        }

        $this->close($f);
    }

    /**
     * Returns the full URL for a given route
     * @param array $route The route data
     * @return string The url
     */
    protected function getUrl(array $route) : string
    {
        $base_url = $this->app->lang->getUrlByCode($route['language']);

        if ($route['route'] == '/') {
            return $base_url;
        }
        
        return $base_url . '/' . $route['route'];
    }

    /**
     * Returns the last modified date for a given route
     * @param array $route The route data
     * @return string The last modified date in Y-m-d format, or an empty string
     */
    protected function getLastModified(array $route) : string
    {
        $type = $route['type'];
        if ($type != 'page' && $type != 'template') {
            return '';
        }

        $filename = $route['data']['filename'] ?? '';
        if (!$filename) {
            return '';
        }

        return date('Y-m-d', filemtime($filename));
    }
}
