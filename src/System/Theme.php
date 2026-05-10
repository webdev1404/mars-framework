<?php
/**
* The System's Theme Class
* @package Mars
*/

namespace Mars\System;

use Mars\Extensions\Themes\Theme as BaseTheme;

use Mars\App;

/**
 * The System's Theme Class
 */
class Theme extends BaseTheme
{
    /**
     * @var bool $is_homepage Set to true if the homepage is currently displayed
     */
    public bool $is_homepage {
        get => $this->app->is_homepage;
    }

    /**
     * @var string $parent_name The name of the parent theme, if any
     */
    public protected(set) string $parent_name = '';

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
     * Outputs the generated content
     * @param string $content The content to output
     */
    public function output(string $content = '')
    {
        $this->prepare();

        $this->renderHeader();
        $this->renderContent($content);
        $this->renderFooter();
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
     * @param string $code The css code to output
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
        echo $this->app->timer->getExecutionTime();
    }

    /**
     * Outputs the memory usage
     */
    public function outputMemoryUsage()
    {
        echo round(memory_get_peak_usage(true) / (1024 * 1024), 4);
    }

    /**
     * Outputs all the alerts: messages/errors/info/warnings
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

    /**
     * Outputs the site name
     */
    public function outputSiteName()
    {
        echo $this->app->escape->html($this->app->config->site->name);
    }

    /**
     * Outputs the site slogan
     */
    public function outputSiteSlogan()
    {
        echo $this->app->escape->html($this->app->config->site->slogan);
    }

    /**
     * Outputs a menu
     * @param string $menu The menu [Eg: main, footer, etc]
     */
    public function outputMenu(string $menu) 
    {
        if (!isset($this->app->menus->$menu)) {
            throw new \Exception("Menu '{$menu}' not found");
        }

        echo $this->app->menus->$menu->output();
    }

    /**
     * @see BaseTheme::getTemplateFilename()
     * {@inheritDoc}
     */
    public function getTemplateFilename(string $template) : ?string
    {
        $template_filename = parent::getTemplateFilename($template);
        if ($template_filename) {
            return $template_filename;
        }

        //if we have a parent, check the parent's templates
        if ($this->parent) {
            $template_filename = $this->parent->getTemplateFilename($template);
            if ($template_filename) {
                return $template_filename;
            }
        }

        return null;
    }

    /**
     * @see BaseTheme::getTemplateFromFilename()
     * {@inheritDoc}
     */
    public function getTemplateFromFilename(string $filename, ?string $filename_rel = null, array $vars = [], string $type = 'template', array $params = [], bool $development = false) : string
    {
        if ($filename_rel) {
            if (!isset($this->templates[$filename_rel])) {
                //does the parent have it?
                if ($this->parent) {
                    if (isset($this->parent->templates[$filename_rel])) {
                        $filename = $this->parent->templates_path . '/' . $filename_rel;
                    }
                }
            }
        }

        return parent::getTemplateFromFilename($filename, $filename_rel, $vars, $type, $params, $development);
    }

    /**
     * @see LanguagesTrait::loadLanguage
     */
    public function loadLanguage(string $file, ?string $key = null) : static
    {
        if ($this->parent) {
            $this->parent->loadLanguage($file, $key);
        }

        return parent::loadLanguage($file, $key);
    }
}
