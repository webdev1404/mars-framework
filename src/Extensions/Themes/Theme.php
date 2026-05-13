<?php
/**
* The Theme Class
* @package Mars
*/

namespace Mars\Extensions\Themes;

use Mars\App;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\App\HiddenProperty;
use Mars\Document;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;
use Mars\Extensions\Themes\Links\Css;
use Mars\Extensions\Themes\Links\Javascript;
use Mars\Extensions\Themes\Links\Favicon;
use Mars\Extensions\Themes\Links\Fonts;
use Mars\Extensions\Themes\Links\Images;
use Mars\Extensions\Abilities\ConfigTrait;
use Mars\Extensions\Abilities\FilesCacheTrait;
use Mars\Extensions\Abilities\LanguagesTrait;
use Mars\Themes\Template;

/**
 * The Theme Class
 */
class Theme extends Extension
{
    use LazyLoad;
    use FilesCacheTrait;
    use LanguagesTrait;

    /**
     * @internal
     */
    public const array DIRS = [
        'assets' => 'assets',
        'images' => 'images',
        'config' => 'config',
        'css' => 'css',
        'fonts' => 'fonts',
        'js' => 'js',
        'libraries' => 'libraries',
        'languages' => 'languages',
        'templates' => 'templates',
        'src' => 'src',
        'setup' => 'Setup',
    ];

    /**
     * @const array CACHE_DIRS The dirs to be cached
     */
    public const array CACHE_DIRS = ['templates'];

    /**
     * @const array MOBILE_DORS The locations of the used mobile subdirs
     */
    public const array MOBILE_DIRS = [
        'mobile' => 'mobile',
        'tablets' => 'tablets',
        'smartphones' => 'smartphones'
    ];

    /**
     * @var Document $document The document object
     */
    public Document $document {
        get => $this->app->document;
    }

    /**
     * @var Css $css The css object
     */
    #[LazyLoadProperty]
    public Css $css;

    /**
     * @var Javascript $js The javascript object
     */
    #[LazyLoadProperty]
    public Javascript $js;

    /**
     * @var Favicon $favicon The favicon object
     */
    #[LazyLoadProperty]
    public Favicon $favicon;
    
    /**
     * @var Fonts $fonts The fonts object
     */
    #[LazyLoadProperty]
    public Fonts $fonts;

    /**
     * @var Images $images The images object
     */
    #[LazyLoadProperty]
    public Images $images;

    /**
     * @var Template $template The engine used to parse the template
     */
    #[LazyLoadProperty]
    #[HiddenProperty]
    public protected(set) Template $template;

    /**
     * @internal
     */
    protected static array $lazyload_add_this = [
       Css::class,
       Javascript::class,
       Favicon::class,
       Fonts::class,
       Images::class,
    ];

    /**
     * @var string $header_template The template which will be used to render the header
     */
    public string $header_template = 'header';

    /**
     * @var string $footer_template The template which will be used to render the footer
     */
    public string $footer_template = 'footer';

    /**
     * @var string $content_template The template which will be used to render the content
     */
    public string $content_template = 'content';

    /**
     * @var string $images_path The path for the theme's images folder
     */
    public protected(set) string $images_path {
        get {
            if (isset($this->images_path)) {
                return $this->images_path;
            }

            $this->images_path = $this->assets_path . '/' . static::DIRS['images'];

            return $this->images_path;
        }
    }

    /**
     * @var string $images_url The url of the theme's images folder
     */
    public protected(set) string $images_url {
        get {
            if (isset($this->images_url)) {
                return $this->images_url;
            }

            $this->images_url = $this->assets_url . '/' . rawurlencode(static::DIRS['images']);

            return $this->images_url;
        }
    }

    /**
     * @var string $css_path The path for the theme's css folder
     */
    public protected(set) string $css_path {
        get {
            if (isset($this->css_path)) {
                return $this->css_path;
            }

            $this->css_path = $this->assets_path . '/' . static::DIRS['css'];

            return $this->css_path;
        }
    }
    /**
     * @var string $css_url The url of the theme's css folder
     */
    public protected(set) string $css_url {
        get {
            if (isset($this->css_url)) {
                return $this->css_url;
            }

            $this->css_url = $this->assets_url . '/' . rawurlencode(static::DIRS['css']);

            return $this->css_url;
        }
    }

    /**
     * @var string $js_path The path for the theme's js folder
     */
    public protected(set) string $js_path {
        get {
            if (isset($this->js_path)) {
                return $this->js_path;
            }

            $this->js_path = $this->assets_path . '/' . static::DIRS['js'];

            return $this->js_path;
        }
    }

    /**
     * @var string $js_url The url of the theme's js folder
     */
    public protected(set) string $js_url {
        get {
            if (isset($this->js_url)) {
                return $this->js_url;
            }

            $this->js_url = $this->assets_url . '/' . rawurlencode(static::DIRS['js']);

            return $this->js_url;
        }
    }

    /**
     * @var string $library_path The path for the currently loaded library. It will be set when loading a library with loadLibrary() method.
     */
    public protected(set) string $library_path = '';

    /**
     * @var string $library_url The url for the currently loaded library. It will be set when loading a library with loadLibrary() method.
     */
    public protected(set) string $library_url = '';

    /**
     * @var string $templates_path The path for the theme's templates folder
     */
    public protected(set) string $templates_path {
        get {
            if (isset($this->templates_path)) {
                return $this->templates_path;
            }

            $this->templates_path = $this->path . '/' . static::DIRS['templates'];

            return $this->templates_path;
        }
    }

    /**
     * @var array $templates Array with the list of available templates
     */
    public array $templates {
        get => $this->files_cache_list['templates'] ?? [];
    }

    /**
     * @var bool $has_mobile_templates If true, the theme has mobile templates
     */
    public protected(set) bool $has_mobile_templates {
        get {
            if (isset($this->has_mobile_templates)) {
                return $this->has_mobile_templates;
            }

            $this->has_mobile_templates = isset($this->templates[static::MOBILE_DIRS['mobile']]);

            return $this->has_mobile_templates;
        }
    }

    /**
     * @var array Array with the list of loaded templates
     */
    public protected(set) array $templates_loaded = [];

    /**
     * @var array $vars The theme's vars are stored here
     */
    public array $vars = [];

    /**
     * @var string $content The generated content
     */
    protected string $content = '';

    /**
     * @internal
     */
    protected static string $manager_class = Themes::class;

    /**
     * @internal
     */
    protected static ?Extensions $manager_instance = null;

    /**
     * @internal
     */
    protected static string $type = 'theme';

    /**
     * @internal
     */
    protected static string $base_dir = 'themes';

    /**
     * @internal
     */
    protected static string $base_namespace = "\\Themes";

    /**
     * Builds the theme
     * @param string $name The name of the exension
     * @param array $params The params passed to the theme, if any
     * @param App $app The app object
     */
    public function __construct(string $name, array $params = [], ?App $app = null)
    {
        $this->lazyLoad($app);

        parent::__construct($name, $params, $app);
    }

    /**
     * Prepares the theme. It will be called before output the theme
     */
    public function prepare()
    {
        $app = $this->app;

        if (is_file($this->path . '/prepare.php')) {
            include($this->path . '/prepare.php');
        }
    }

    /**
     * Returns a data value.
     * @param string $name The name of the data
     * @return mixed The data value
     */
    public function getData(string $name)
    {
        return $this->template->data[$name] ?? null;
    }

    /**
     * Returns a theme variable.
     * @param string $name The name of the var
     * @return static
     */
    public function getVar(string $name)
    {
        return $this->vars[$name] ?? null;
    }
    
    /**
     * Adds a theme variable.
     * @param string $name The name of the var
     * @param mixed $value The value of the var
     * @return static
     */
    public function addVar(string $name, $value) : static
    {
        $this->vars[$name] = $value;

        return $this;
    }

    /**
     * Adds template variables
     * @param array $vars Adds each element [$name=>$value] from $values as theme variables
     * @return static
     */
    public function addVars(array $vars) : static
    {
        if (!$vars) {
            return $this;
        }

        $this->vars = array_merge($this->vars, $vars);

        return $this;
    }

    /**
     * Unsets a theme variable
     * @param string $name The name of the var
     * @return static
     */
    public function unsetVar(string $name) : static
    {
        unset($this->vars[$name]);

        return $this;
    }

    /**
     * Unsets theme variables
     * @param array $values Array with the name of the vars to unset
     * @return static
     */
    public function unsetVars(array $values) : static
    {
        foreach ($values as $name) {
            unset($this->vars[$name]);
        }

        return $this;
    }

    /**
     * Returns the template filename. It will check if the template exists in the mobile templates, if the device is mobile.
     * If the device is a smartphone, it will check for the smartphone template first, then the tablet template, and finally the generic mobile template.
     * @param string $template The name of the template
     * @return ?string The template filename or null if not found
     */
    public function getTemplateFilename(string $template) : ?string
    {
        $template_filename = null;
        $template = $template . '.php';

        if ($this->has_mobile_templates && $this->app->device->is_mobile) {
            if ($this->app->device->is_smartphone) {
                $filename = static::MOBILE_DIRS['mobile'] . '/' . static::MOBILE_DIRS['smartphones'] . '/' . $template;
                
                if (isset($this->templates[$filename])) {
                    $template_filename = $filename;
                }
            } elseif ($this->app->device->is_tablet) {
                $filename = static::MOBILE_DIRS['mobile'] . '/' . static::MOBILE_DIRS['tablets'] . '/' . $template;
                if (isset($this->templates[$filename])) {
                    $template_filename = $filename;
                }
            }

            if (!$template_filename) {
                $filename = static::MOBILE_DIRS['mobile'] . '/' . $template;
                if (isset($this->templates[$filename])) {
                    $template = $filename;
                }
            }
        }

        if (!$template_filename) {
            if (isset($this->templates[$template])) {
                $template_filename = $template;
            }
        }

        if ($template_filename) {
            return $this->templates_path . '/' . $template_filename;
        }

        return $template_filename;
    }

    /**
     * Renders/Outputs a template
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     */
    public function render(string $template, array $vars = [])
    {
        echo $this->template->render($template, $vars);
    }

    /**
     * Renders/Outputs a template, by filename
     * @param string $filename The filename of the template
     * @param array $vars Vars to pass to the template, if any
     */
    public function renderFilename(string $filename, array $vars = [])
    {
        echo $this->template->renderFilename($filename, $vars);
    }

    /**
     * Loads a template and returns it's content
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     * @return string The template content
     */
    public function getTemplate(string $template, array $vars = []) : string
    {
        if ($this->app->config->debug->enable) {
            $this->templates_loaded[] = $template;
        }

        return $this->template->get($template, $vars);
    }

    /**
     * Loads a template and returns it's content
     * @param string $filename The filename of the template
     * @param string $filename_rel The relative filename of the template
     * @param array $vars Vars to pass to the template, if any
     * @param string $type The template's type, if any
     * @param array $params The template's params, if any
     * @param bool $development If true, the template will be parsed in development mode
     * @return string The template content
     */
    public function getTemplateFromFilename(string $filename, ?string $filename_rel = null, array $vars = [], string $type = 'template', array $params = [], bool $development = false) : string
    {
        if ($this->app->config->debug->enable) {
            $this->templates_loaded[] = $filename;
        }

        if ($filename_rel) {
            if (isset($this->templates[$filename_rel])) {
                $filename = $this->templates_path . '/' . $filename_rel;
            }
        }

        return $this->template->getFromFilename($filename, $vars, $type, $params, $development);
    }

    /**
     * Loads a library
     * @param string $name The name of the library
     * @return static
     */
    public function loadLibrary(string $name, $base_dir = 'node_modules') : static
    {
        $this->library_path = $this->assets_path . '/' . static::DIRS['libraries'] . '/' . $base_dir . '/' . $name;
        $this->library_url = $this->assets_url . '/' . urlencode(static::DIRS['libraries']) . '/' . urlencode($base_dir) . '/' . $name;

        require($this->library_path . '/boot.php');

        $this->library_path = '';
        $this->library_url = '';

        return $this;
    }
}
