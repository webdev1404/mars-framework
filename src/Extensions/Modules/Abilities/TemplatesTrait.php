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
        $filename = $this->path . '/' . static::DIRS['templates'] . '/' . $file;
        $rel_filename = $this->path_rel . '/' . $file;

        return $this->app->theme->getTemplateFromFilename($filename, $rel_filename, $vars, static::$type, [], $this->development);
    }

    /**
     * Loads an email template from the extension's templates dir
     * @param string $template The name of the template to load
     * @param array $vars Vars to pass to the template, if any
     * @return string The contents of the template
     */
    public function getEmailTemplate(string $template, array $vars = []) : string
    {
        $content = '';
        $file = $template . '.php';
        $filename = $this->path . '/' . static::DIRS['templates'] . '/' . $file;
        $rel_filename = $this->path_rel . '/' . $file;
        
        $language_filename = $this->app->lang->getTemplateFilename($rel_filename);
        if ($language_filename) {
            //do we have a template set in the current's language templates folder?
            $content = $this->app->theme->getTemplateFromFilename($language_filename, null, $vars, static::$type, [], $this->development);
        } else {
            //check if we have a language-specific template in the extension's templates folder
            $template_filename = $this->getLanguageTemplateFilename($filename, $rel_filename) ?? $filename;
            if ($template_filename) {
                [$filename, $rel_filename] = $template_filename;
            }

            $content = $this->app->theme->getTemplateFromFilename($filename, $rel_filename, $vars, static::$type, [], $this->development);
        }

        return nl2br($content);
    }

    /**
     * Returns the filename of a template in the current language
     * @param string $filename The original filename of the template
     * @return array|null An array containing the filename and the relative filename, or null if no language-specific template was found
     */
    protected function getLanguageTemplateFilename(string $filename, string $rel_filename) : ?array
    {
        $dir = dirname($filename);
        $rel_dir = dirname($rel_filename);
        $file = basename($filename);

        $template_filename = $dir . '/' . $this->app->lang->name . '/' . $file;
        if (is_file($template_filename)) {
            return [$template_filename, $rel_dir . '/' . $this->app->lang->name . '/' . $file];
        }

        if ($this->app->lang->parent) {
            $template_filename = $dir . '/' . $this->app->lang->parent->name . '/' . $file;
            if (is_file($template_filename)) {
                return [$template_filename, $rel_dir . '/' . $this->app->lang->parent->name . '/' . $file];
            }
        }

        if ($this->app->lang->fallback) {
            $template_filename = $dir . '/' . $this->app->lang->fallback->name . '/' . $file;
            if (is_file($template_filename)) {
                return [$template_filename, $rel_dir . '/' . $this->app->lang->fallback->name . '/' . $file];
            }
        }

        return null;
    }
}
