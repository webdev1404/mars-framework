<?php
/**
* The Theme Class
* @package Mars
*/

namespace Mars\Extensions\Themes;

use Mars\App;
use Mars\App\LazyLoad;
use Mars\LazyLoadProperty;
use Mars\HiddenProperty;
use Mars\Document;
use Mars\Extensions\Extension;
use Mars\Extensions\Extensions;
use Mars\Extensions\Themes\Links\Css;
use Mars\Extensions\Themes\Links\Javascript;
use Mars\Extensions\Themes\Links\Favicon;
use Mars\Extensions\Themes\Links\Fonts;
use Mars\Extensions\Themes\Links\Images;
use Mars\Extensions\Abilities\FilesCacheTrait;
use Mars\Extensions\Abilities\LanguagesTrait;
use Mars\Themes\Template;
use Mars\System\Libraries;

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
        'css' => 'css',
        'fonts' => 'fonts',
        'js' => 'js',
        'languages' => 'languages',
        'templates' => 'templates',
        'src' => 'src',
        'setup' => 'Setup',
    ];

    /**
     * @const array CACHE_DIRS The dirs to be cached
     */
    public const array CACHE_DIRS = ['templates', 'languages'];

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
     * @var Libraries $libraries The libraries object
     */
    #[HiddenProperty]
    public Libraries $libraries {
        get => $this->app->libraries;
    }

    /**
     * @var Template $template The engine used to parse the template
     */
    #[LazyLoadProperty]
    #[HiddenProperty]
    public protected(set) Template $template;

    /**
     * The list of properties to be lazy loaded, with this passed as the first param to the constructor
     * @var array $lazyload_add_this
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
     * Outputs the header
     */
    public function renderHeader()
    {
        echo $this->getTemplate($this->header_template);
    }

    /**
     * Outputs the content template
     * @param string $content The content to render
     */
    public function renderContent(string $content)
    {
        $this->content = $content;

        echo $this->getTemplate($this->content_template, ['content' => $content]);
    }

    /**
     * Outputs the footer
     */
    public function renderFooter()
    {
        echo $this->getTemplate($this->footer_template);
    }

    /**
     * Outputs the language
     */
    public function outputLang()
    {
        echo $this->app->escape->html($this->app->lang->lang);
    }

    /**
     * Outputs code in the <head>
     */
    public function outputHead()
    {
        $this->document->outputHead();

        $this->app->plugins->run('theme.output.head', $this);
    }

    /**
     * Outputs code in the footer
     */
    public function outputFooter()
    {
        $this->document->outputFooter();

        $this->app->plugins->run('theme.output.footer', $this);
    }

    /**
     * Outputs the generated content
     */
    public function outputContent()
    {
        echo $this->content;

        $this->app->plugins->run('theme.output.content', $this);
    }

    /**
     * Outputs css inline code
     * @param string $code The js code to output
     */
    public function outputCssCode(string $code)
    {
        $this->css->outputCode($code);
    }

    /**
     * Outputs javascript inline code
     * @param string $code The js code to output
     */
    public function outputJsCode(string $code)
    {
        $this->js->outputCode($code);
    }

    /**
     * Outputs the execution time
     */
    public function outputExecutionTime()
    {
        return $this->app->timer->getExecutionTime();
    }

    /**
     * Returns the memory usage
     */
    public function outputMemoryUsage()
    {
        return round(memory_get_peak_usage(true) / (1024 * 1024), 4);
    }

    /**
     * Outputs all the alers: messages/errors/info/warnings
     */
    public function outputAlerts()
    {
        $this->outputMessages();
        $this->outputErrors();
        $this->outputInfo();
        $this->outputWarnings();
    }

    /**
     * Outputs the errors
     */
    public function outputErrors()
    {
        $errors = $this->getErrors();
        if (!$errors) {
            return;
        }

        $this->addVar('errors', $errors);

        $this->render('alerts/errors');
    }

    /**
     * Returns the errors
     * @return array The errors, if any
     */
    public function getErrors() : array
    {
        $errors = $this->app->errors->get();
        if (!$errors) {
            return [];
        }

        $max_errors = 5;
        $errors_count = count($errors);

        //display only the first $max_errors errors.
        if ($errors_count > $max_errors) {
            $errors = array_slice($errors, 0, $max_errors);
            $errors[] = '....................';
        }

        return $errors;
    }

    /**
     * Outputs the messages
     */
    public function outputMessages()
    {
        if ($this->app->errors->count()) {
            return;
        }

        $messages = $this->app->messages->get();
        if (!$messages) {
            return;
        }

        $this->addVar('messages', $messages);

        $this->render('alerts/messages');
    }

    /**
     * Outputs the info
     */
    public function outputInfo()
    {
        $info = $this->app->info->get();
        if (!$info) {
            return;
        }

        $this->addVar('info', $info);

        $this->render('alerts/info');
    }

    /**
     * Outputs the warnings
     */
    public function outputWarnings()
    {
        $warnings = $this->app->warnings->get();
        if (!$warnings) {
            return;
        }

        $this->addVar('warnings', $warnings);

        $this->render('alerts/warnings');
    }
}
