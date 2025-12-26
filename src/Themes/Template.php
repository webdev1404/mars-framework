<?php
/**
* The Template Engine Class
* @package Mars
*/

namespace Mars\Themes;

use Mars\App\Kernel;
use Mars\App\Drivers;
use Mars\Extensions\Theme;
use Mars\Themes\Templates\TemplateInterface;

/**
 * The Template Engine Class
 */
class Template
{
    use Kernel;

    /**
     * @var array $supported_drivers The supported drivers
     */
    public protected(set) array $supported_drivers = [
        'mars' => \Mars\Themes\Templates\Mars::class
    ];

    /**
     * @var Drivers $drivers The drivers object
     */
    public protected(set) Drivers $drivers {
        get {
            if (isset($this->drivers)) {
                return $this->drivers;
            }

            $this->drivers = new Drivers($this->supported_drivers, TemplateInterface::class, 'templates', $this->app);

            return $this->drivers;
        }
    }

    /**
     * @var TemplateInterface $driver The driver object
     */
    public protected(set) TemplateInterface $driver {
        get {
            if (isset($this->driver)) {
                return $this->driver;
            }

            $this->driver = $this->drivers->get($this->app->config->templates->driver);

            return $this->driver;
        }
    }

    /**
     * @var array $data Data set in the array
     */
    public protected(set) array $data = [];

    /**
     * @var bool $development If true, the templates will be parsed in development mode
     */
    protected bool $development {
        get => $this->app->theme->development;
    }

    /**
     * Renders/Outputs a template
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     */
    public function render(string $template, array $vars = [])
    {
        echo $this->get($template, $vars);
    }

    /**
     * Renders/Outputs a template, by filename
     * @param string $filename The filename of the template
     * @param array $vars Vars to pass to the template, if any
     */
    public function renderFilename(string $filename, array $vars = [])
    {
        echo $this->getFromFilename($filename, $vars);
    }

    /**
     * Loads a template from the theme's templates dir and returns it's content
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     * @param string $type The template's type, if any
     * @return string The template content
     */
    public function get(string $template, array $vars = [], string $type = 'template') : string
    {
        $filename = $this->getFilename($template);
        $cache_name = $this->app->cache->templates->getName($filename, $type);

        $content = $this->getContent($filename, $cache_name, $vars);

        return $this->app->plugins->filter('template.get', $content, $template, $vars, $type, $this);
    }

    /**
     * Loads a template and returns it's content
     * @param string $filename The filename of the template
     * @param array $vars Vars to pass to the template, if any
     * @param string $type The template's type, if any
     * @param array $params Params to pass to the parser
     * @param bool $development True if the template should be parsed in development mode
     * @return string The template content
     */
    public function getFromFilename(string $filename, array $vars = [], string $type = 'template', array $params = [], bool $development = false) : string
    {
        $cache_file = $this->app->cache->templates->getName($filename, $type);

        $content = $this->getContent($filename, $cache_file, $vars, $params, $development);

        return $this->app->plugins->filter('template.get.from.filename', $content, $filename, $vars, $type, $this);
    }

    /**
     * Returns the filename corresponding to $template
     * @param string $template The name of the template
     * @return string The filename
     */
    public function getFilename(string $template) : string
    {
        $filename = $this->app->theme->getTemplateFilename($template);
        if (!$filename) {
            throw new \Exception("Template not found: {$template}");
        }

        return $filename;
    }

    /**
     * Returns the contents of a template
     * @param string $filename The filename from where the template will be loaded
     * @param string $cache_file The name used to cache the template
     * @param array $vars Vars to pass to the template, if any
     * @param array $params Params to pass to the parser
     * @param bool $development If true, won't cache the template
     * @return string The template content
     */
    protected function getContent(string $filename, string $cache_name, array $vars = [], array $params = [], bool $development = false) : string
    {
        if ($this->development || $development || !$this->app->cache->templates->exists($cache_name)) {
            $this->write($filename, $cache_name, ['filename' => $filename] + $params);
        }

        $content = $this->incorporate($cache_name, $vars);

        $content = $this->app->plugins->filter('template.get.content', $content, $filename, $cache_name, $vars, $this);

        return $content;
    }

    /**
     * Parses the template content and returns it
     * @param string $content The content to parse
     * @param array $params Params to pass to the parser
     * @return string The parsed content
     */
    public function parse(string $content, array $params) : string
    {
        return $this->driver->parse($content, $params);
    }

    /**
     * Loads $filename, parses it and then writes it in the cache folder
     * @param string $filename The filename from where the template will be loaded
     * @param string $cache_filename The filename used to cache the template
     * @param array $params Params to pass to the parser
     * @throws \Exception If the file can't be read or written to the cache
     */
    protected function write(string $filename, string $cache_filename, array $params)
    {
        $content = file_get_contents($filename);

        if ($content === false) {
            throw new \Exception("Error reading template file: {$filename}");
        }

        $content = $this->parse($content, $params);

        return $this->app->cache->templates->write($cache_filename, $content);
    }

    /**
     * Includes a template and returns it's content
     * @param string $cache_name The name of the cached template
     * @param array $vars Vars to pass to the template, if any
     * @return string The template's content
     */
    protected function incorporate(string $cache_name, array $vars = []) : string
    {
        $this->data = [];

        $app = $this->app;
        $lang = $this->app->lang;
        $theme = $this->app->theme;
        $modules = $this->app->modules;
        $config = $this->app->config;
        $html = $this->app->html;
        $ui = $this->app->ui;
        $url = $this->app->url;
        $format = $this->app->format;
        $plugins = $this->app->plugins;
        $request = $this->app->request;
        $get = $this->app->request->get;
        $post = $this->app->request->post;
        $document = $this->app->document;
        $strings = $this->app->lang->strings;
        
        extract($this->app->theme->vars);
        extract($vars);

        ob_start();

        include($this->app->cache->templates->getFilename($cache_name));

        return ob_get_clean();
    }
}
