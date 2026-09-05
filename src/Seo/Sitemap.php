<?php

namespace Mars\Seo;

use Mars\App\Kernel;
use Mars\App\Handlers;

class Sitemap
{
    use Kernel;

    /**
     * @var array $supported_generators The list of supported generators
     */
    public protected(set) array $supported_generators = [
        'routes' => \Mars\Seo\Sitemap\Routes::class,
        'extensions' => \Mars\Seo\Sitemap\Extensions::class,
    ];

    /**
     * @var Handlers $generators The generators object
     */
    public protected(set) Handlers $generators {
        get {
            if (isset($this->generators)) {
                return $this->generators;
            }

            $this->generators = new Handlers($this->supported_generators, null, $this->app);

            return $this->generators;
        }
    }

    /**
     * Generates the sitemap for all supported generators
     */
    public function generate()
    {
        if (!is_writable($this->app->public_path)) {
            throw new \Exception("Public path {$this->app->public_path} is not writable. The sitemap cannot be generated.");
        }

        $this->app->cache->sitemap->clean();

        $sitemaps = [];
        foreach ($this->generators as $generator) {
            $generator_sitemaps = $generator->generate();
            $sitemaps = array_merge($sitemaps, $generator_sitemaps);
        }

        $this->write($sitemaps);
    }

    /**
     * Writes the sitemap index to the public directory.
     * @param array $sitemaps The list of sitemaps
     */
    protected function write(array $sitemaps)
    {
        if (!$sitemaps) {
            throw new \Exception("No sitemaps to write.");
        }

        $filename = $this->app->public_path . '/sitemap_index.xml';

        $cnt = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $cnt .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $sitemap) {
            $url = $this->app->assets_url . '/cache/sitemap/' . $sitemap;
            $cnt .= '    <sitemap>' . "\n";
            $cnt .= '        <loc>' . $this->app->escape->html($url) . '</loc>' . "\n";
            $cnt .= '    </sitemap>' . "\n";
        }
        $cnt .= '</sitemapindex>';

        if (!file_put_contents($filename, $cnt)) {
            throw new \Exception("Failed to write sitemap index: {$filename}");
        }
    }
}
