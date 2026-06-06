<?php
/**
* The System's Theme Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\LazyLoadProperty;
use Mars\App\HiddenProperty;
use Mars\Extensions\Themes\Theme as BaseTheme;
use Mars\Themes\Template;

/**
 * The System's Theme Class
 */
class Theme extends BaseTheme
{
    /**
     * @const array MOBILE_DORS The locations of the used mobile subdirs
     */
    public const array MOBILE_DIRS = [
        'mobile' => 'mobile',
        'tablet' => 'tablets',
        'smartphone' => 'smartphones'
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
     * @var array $vars The theme's vars are stored here
     */
    public array $vars = [];

    /**
     * @var bool $is_homepage Set to true if the homepage is currently displayed
     */
    public bool $is_homepage {
        get => $this->app->is_homepage;
    }

    /**
     * @var string $content The generated content
     */
    protected string $content = '';

    /**
     * @var BaseTheme $parent The parent theme, if any
     */
    public protected(set) ?BaseTheme $parent {
        get {
            if (isset($this->parent)) {
                return $this->parent;
            }

            $this->parent = null;

            if ($this->parent_name) {
                $this->parent = new BaseTheme($this->parent_name, [], $this->app);
                $this->parent->boot();
            }

            return $this->parent;
        }
    }

    /**
     * @var Template $template The engine used to parse the template
     */
    #[LazyLoadProperty]
    #[HiddenProperty]
    public protected(set) Template $template;

    /**
     * @var array $templates Array with the list of available templates
     */
    public protected(set) array $templates {
        get {
            if (isset($this->templates)) {
                return $this->templates;
            }

            $this->templates = $this->getTemplates();

            return $this->templates;
        }
    }

    /**
     * @var array Array with the list of loaded templates
     */
    public protected(set) array $templates_loaded = [];

    /**
     * Builds the theme
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        if (!$app->config->theme->name) {
            throw new \Exception('No theme set in config');
        }

        parent::__construct($app->config->theme->name, [], $app);

        $this->boot();
    }

    /**
     * Builds the theme's HTML code and renders it
     * @param string $content The content
     */
    public function renderHtml(string $content = '')
    {
        $this->content = $content;

        $this->prepare();

        $this->render($this->header_template);
        $this->render($this->content_template);
        $this->render($this->footer_template);
    }

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        if ($this->parent) {
            $this->parent->prepare();
        }

        parent::prepare();
    }

    /**
     * Renders a template
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     */
    public function render(string $template, array $vars = [])
    {
        echo $this->getTemplate($template, $vars);
    }

    /**
     * Renders a template, by filename
     * @see Theme::getTemplateByFilename()
     */
    public function renderFilename(string $filename, ?string $filename_rel = null, array $vars = [], string $type = 'template', array $params = [], bool $development = false)
    {
        echo $this->getTemplateByFilename($filename, $filename_rel, $vars, $type, $params, $development);
    }

    /**
     * Renders the language
     */
    public function renderLang()
    {
        echo $this->app->escape->html($this->app->lang->lang);
    }

    /**
     * Renders code in the <head>
     */
    public function renderHead()
    {
        $this->document->renderHead();

        $this->app->plugins->run('theme.render.head', $this);
    }

    /**
     * Renders code in the footer
     */
    public function renderFooter()
    {
        $this->document->renderFooter();

        $this->app->plugins->run('theme.render.footer', $this);
    }

    /**
     * Renders the generated content
     */
    public function renderContent()
    {
        echo $this->content;

        $this->app->plugins->run('theme.render.content', $this);
    }

    /**
     * Renders css inline code
     * @param string $code The css code to render
     */
    public function renderCssCode(string $code)
    {
        $this->css->renderCode($code);
    }

    /**
     * Renders javascript inline code
     * @param string $code The js code to render
     */
    public function renderJsCode(string $code)
    {
        $this->js->renderCode($code);
    }

    /**
     * Renders the execution time
     */
    public function renderExecutionTime()
    {
        echo $this->app->timer->getExecutionTime();
    }

    /**
     * Renders the memory usage
     */
    public function renderMemoryUsage()
    {
        echo round(memory_get_peak_usage(true) / (1024 * 1024), 4);
    }

    /**
     * Renders all the alerts: messages/errors/info/warnings
     */
    public function renderAlerts()
    {
        $this->renderMessages();
        $this->renderErrors();
        $this->renderInfo();
        $this->renderWarnings();
    }

    /**
     * Renders the errors
     */
    public function renderErrors()
    {
        $errors = $this->getErrors();
        if (!$errors) {
            return;
        }

        $this->addVar('errors', $errors);

        $this->render('alert/errors');
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
     * Renders the messages
     */
    public function renderMessages()
    {
        if ($this->app->errors->count()) {
            return;
        }

        $messages = $this->app->messages->get();
        if (!$messages) {
            return;
        }

        $this->addVar('messages', $messages);

        $this->render('alert/messages');
    }

    /**
     * Renders the info
     */
    public function renderInfo()
    {
        $info = $this->app->info->get();
        if (!$info) {
            return;
        }

        $this->addVar('info', $info);

        $this->render('alert/info');
    }

    /**
     * Renders the warnings
     */
    public function renderWarnings()
    {
        $warnings = $this->app->warnings->get();
        if (!$warnings) {
            return;
        }

        $this->addVar('warnings', $warnings);

        $this->render('alert/warnings');
    }

    /**
     * Renders the site name
     */
    public function renderSiteName()
    {
        echo $this->app->escape->html($this->app->config->site->name);
    }

    /**
     * Renders the site slogan
     */
    public function renderSiteSlogan()
    {
        echo $this->app->escape->html($this->app->config->site->slogan);
    }

    /**
     * Renders a menu
     * @param string $menu The menu [Eg: main, footer, etc]
     */
    public function renderMenu(string $menu)
    {
        if (!isset($this->app->menus->$menu)) {
            throw new \Exception("Menu '{$menu}' not found");
        }

        echo $this->app->menus->$menu->render();
    }

    /**
     * Returns the templates the theme has, by device
     */
    protected function getTemplates() : array
    {
        $cache_key = $this->name . '-' . $this->app->device->type->value . '-templates';

        $templates = $this->app->cache->themes->get($cache_key);
        if ($this->development) {
            $templates = null;
        }

        if ($templates !== null) {
            return $templates;
        }

        $templates = $this->findTemplates();

        $this->app->cache->themes->set($cache_key, $templates);

        return $templates;
    }

    /**
     * Finds the theme's templates, by device. It will check the parent theme(if any) templates as well.
     * @return array The list of templates
     */
    protected function findTemplates() : array
    {
        $templates = $this->readTemplates('', [static::MOBILE_DIRS['mobile']]);

        if ($this->app->device->is_desktop) {
            return $templates;
        }

        //try to locate a mobile template for each of the read templates
        $mobile_templates = $this->readTemplates(static::MOBILE_DIRS['mobile'], [static::MOBILE_DIRS['tablet'], static::MOBILE_DIRS['smartphone']]);
        $templates = array_merge($templates, $mobile_templates);

        $device_dir = static::MOBILE_DIRS[$this->app->device->type->value] ?? null;
        $device_templates = $this->readTemplates(static::MOBILE_DIRS['mobile'] . '/' . $device_dir);
        $templates = array_merge($templates, $device_templates);

        return $templates;
    }

    /**
     * Reads the templates from both the parent theme(if any) and the current theme and adds them to the $templates array
     * @param string $path_suffix The suffix to add to the templates path, if any. Used to read mobile templates from the mobile subdir
     * @param array $exclude_dirs Array with the directories to exclude from the search, if any
     * @return array The list of templates
     */
    protected function readTemplates(string $path_suffix = '', array $exclude_dirs = []) : array
    {
        $templates = [];
        $path_suffix = $path_suffix ? '/' . $path_suffix : '';

        if ($this->parent) {
            $this->readTemplatesFromDir($templates, $this->parent->templates_path . $path_suffix, $exclude_dirs);
        }
        
        $this->readTemplatesFromDir($templates, $this->templates_path . $path_suffix, $exclude_dirs);

        return $templates;
    }

    /**
     * Reads the templates from a directory and adds them to the $templates array
     * @param array $templates The array to add the templates to
     * @param string $path The path to get the templates from
     * @param array $exclude_dirs Array with the directories to exclude from the search, if any
     */
    protected function readTemplatesFromDir(array &$templates, string $path, array $exclude_dirs = [])
    {
        if (!is_dir($path)) {
            return;
        }

        $files = $this->app->dir->get($path, true, false, ['php'], $exclude_dirs);

        foreach ($files as $file) {
            $name = $this->app->file->getFullStem($file);
            
            $templates[$name] = $path . '/' . $file;
        }
    }

    /**
     * Loads a template from the theme's templates dir and returns it's content
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     * @return string The template content
     * @throws \Exception If the template is not found
     */
    public function getTemplate(string $template, array $vars = []) : string
    {
        $filename = $this->templates[$template] ?? null;
        if (!$filename) {
            throw new \Exception("Template '{$template}' not found in theme '{$this->name}'");
        }

        if ($this->app->config->debug->enable) {
            $this->templates_loaded[] = $template;
        }

        return $this->template->get($filename, $vars);
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
    public function getTemplateByFilename(string $filename, ?string $filename_rel = null, array $vars = [], string $type = 'template', array $params = [], bool $development = false) : string
    {
        if ($this->app->config->debug->enable) {
            $this->templates_loaded[] = $filename_rel ?? $filename;
        }

        if ($filename_rel) {
            if (isset($this->templates[$filename_rel])) {
                $filename = $this->templates_path . '/' . $filename_rel;
            }
        }

        return $this->template->get($filename, $vars, $type, $params, $development);
    }

    /**
     * Returns a data value from the last rendered template.
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
}
