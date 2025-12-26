<?php
/**
* The Extension's Templates Trait
* @package Mars
*/

namespace Mars\Extensions\Modules\Abilities;

/**
 * The Extension's Templates Trait
 * Trait which allows extensions to load templates
 */
trait TemplatesTrait
{
    /**
     * @var string $templates_path The path to the extension's templates dir
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
     * @var array $templates The list of template files in the extension
     */
    public protected(set) array $templates {
        get {
            if (isset($this->templates)) {
                return $this->templates;
            }

            $this->templates = $this->files_cache_list[static::DIRS['templates']] ?? [];

            return $this->templates;
        }
    }

    /**
     * Loads the template and outputs it.
     * @param string $template The name of the template
     * @param array $vars Vars to pass to the template, if any
     */
    public function render(string $template, array $vars = [])
    {
        echo $this->getTemplate($template, $vars);
    }

    /**
     * Loads a template from the extension's templates dir
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     * @return string The contents of the template
     */
    public function getTemplate(string $template, array $vars = []) : string
    {
        $file = $template . '.php';
        $filename = $this->templates_path . '/' . $file;
        $rel_filename = $this->path_rel . '/' . $file;

        return $this->app->theme->getTemplateFromFilename($filename, $rel_filename, $vars, static::$type, [], $this->development);
    }

    /**
     * Loads a language template from the extension's templates dir
     * @param string $dir The directory where the template is located
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     * @return string The contents of the template
     */
    public function getLanguageTemplate(string $dir, string $template, array $vars = []) : string
    {
        $file = $dir . '/' . $template . '.php';
        $rel_filename = $this->path_rel . '/' . $file;

        //do we have a template set in the current's language templates folder?
        $language_filename = $this->app->lang->getTemplateFilename($rel_filename);
        if ($language_filename) {
            return $this->app->theme->getTemplateFromFilename($language_filename, null, $vars, static::$type, [], $this->development);
        }

        //check if we have a language-specific template in the extension's templates folder
        $template_file = $this->getLanguageTemplateFile($dir, $template);
        if ($template_file) {
            $filename = $this->templates_path . '/' . $template_file;
            $rel_filename = $this->path_rel . '/' . $template_file;

            return $this->app->theme->getTemplateFromFilename($filename, $rel_filename, $vars, static::$type, [], $this->development);
        }

        return '';
    }

    /**
     * Returns the filename of a template in the current language
     * @param string $dir The directory where the template is located
     * @param string $template The name of the template to load (without extension)
     * @return string|null The file of the language-specific template, or null if none was found
     */
    protected function getLanguageTemplateFile(string $dir, string $template) : ?string
    {
        $file = $template . '.php';
        $template_file = $dir . '/' . $this->app->lang->name . '/' . $file;

        if (isset($this->templates[$template_file])) {
            return $template_file;
        }

        if ($this->app->lang->parent) {
            $template_file = $dir . '/' . $this->app->lang->parent->name . '/' . $file;
            if (isset($this->templates[$template_file])) {
                return $template_file;
            }
        }

        if ($this->app->lang->fallback) {
            $template_file = $dir . '/' . $this->app->lang->fallback->name . '/' . $file;
            if (isset($this->templates[$template_file])) {
                return $template_file;
            }
        }
        
        $template_file = $dir . '/' . $file;
        if (isset($this->templates[$template_file])) {
            return $template_file;
        }

        return null;
    }
}
