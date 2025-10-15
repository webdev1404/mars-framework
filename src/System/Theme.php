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
        if (!$app->config->theme) {
            throw new \Exception('No theme set in config');
        }

        parent::__construct($app->config->theme, [], $app);

        include($this->path . '/init.php');
    }

    /**
     * @see BaseTheme::getTemplateFilename()
     * {@inheritdoc}
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
}
