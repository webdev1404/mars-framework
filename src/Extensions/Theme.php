<?php
/**
* The Theme Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\LazyLoad;
use Mars\LazyLoad\GhostTrait;
use Mars\Template;
use Mars\Document;
use Mars\Document\Css;
use Mars\Document\Javascript;
use Mars\Document\Fonts;
use Mars\Document\Images;

/**
 * The Theme Class
 */
class Theme extends Extension
{
    use GhostTrait;

    /**
     * @var Document $document The document object
     */
    public Document $document {
        get => $this->app->document;
    }

    /**
     * @var Css $css The css object
     */
    public Css $css {
        get => $this->app->document->css;
    }

    /**
     * @var Javascript $javascript The javascript object
     */
    public Javascript $javascript {
        get => $this->app->document->javascript;
    }

    /**
     * @var Fonts $fonts The fonts object
     */
    public Fonts $fonts {
        get => $this->app->document->fonts;
    }

    /**
     * @var Images $images The images object
     */
    public Images $images {
        get => $this->app->document->images;
    }

    /**
     * @var Templat template The engine used to parse the template
     */
    #[LazyLoad]
    public protected(set) Template $template;

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
    protected string $images_path {
        get {
            if (isset($this->images_path)) {
                return $this->images_path;
            }

            $this->images_path = $this->path . '/' . App::EXTENSIONS_DIRS['images'];

            return $this->images_path;
        }
    }

    /**
     * @var string $images_url The url of the theme's images folder
     */
    protected string $images_url {
        get {
            if (isset($this->images_url)) {
                return $this->images_url;
            }

            $this->images_url = $this->url . '/' . rawurlencode(App::EXTENSIONS_DIRS['images']);

            return $this->images_url;
        }
    }

    /**
     * @var array $vars The theme's vars are stored here
     */
    public array $vars = [];

    /**
     * @var array Array with the list of loaded templates
     */
    public protected(set) array $templates_loaded = [];

    /**
     * @var string $content The generated content
     */
    protected string $content = '';

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
    protected static string $base_namespace = "Themes";

    /**
     * Builds the theme
     * @param string $name The name of the exension
     * @param App $app The app object
     */
    public function __construct(string $name, ?App $app = null)
    {   
        $this->lazyLoad($app);

        parent::__construct($name, [], $app);
    }

    /**
     * {@inheritdoc}
     * @see \Mars\Extensions\ExtensionTrait::getRootNamespace()
     */
    protected function getRootNamespace() : string
    {
        return '';
    }

    /***************** VARS METHODS *********************************/

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

    /************** TEMPLATES METHODS **************************/

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
        if ($this->app->config->debug) {
            $this->templates_loaded[] = $template;
        }

        return $this->template->get($template, $vars);
    }

    /**
     * Loads a template and returns it's content
     * @param string $filename The filename of the template
     * @param array $vars Vars to pass to the template, if any
     * @param string $type The template's type, if any
     * @param array $params The template's params, if any
     * @param bool $development If true, the template will be parsed in development mode
     * @return string The template content
     */
    public function getTemplateFromFilename(string $filename, array $vars = [], string $type = 'template', array $params = [], bool $development = false) : string
    {
        if ($this->app->config->debug) {
            $this->templates_loaded[] = $filename;
        }

        return $this->template->getFromFilename($filename, $vars, $type, $params, $development);
    }

    /**************** RENDER METHODS *************************************/

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

    /**************** OUTPUT METHODS *************************************/

    /**
     * Outputs the language code
     */
    public function outputLangCode()
    {
        echo $this->app->escape->html($this->app->lang->code);
    }

    /**
     * Outputs code in the <head>
     */
    public function outputHead()
    {
        $this->document->outputHead();

        $this->app->plugins->run('theme_output_head', $this);
    }

    /**
     * Outputs code in the footer
     */
    public function outputFooter()
    {
        $this->document->outputFooter();

        $this->app->plugins->run('theme_output_footer', $this);
    }

    /**
     * Outputs the generated content
     */
    public function outputContent()
    {
        echo $this->content;

        $this->app->plugins->run('theme_output_content', $this);
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
    public function outputJavascriptCode(string $code)
    {
        $this->javascript->outputCode($code);
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

    /**************** OUTPUT MESSAGES *************************************/

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
