<?php
/**
* The Template Content Class
* @package Mars
*/

namespace Mars\Content;

/**
 * The Template Content Class
 * Outputs the content of a template
 */
class Template extends Content implements ContentInterface
{
    /**
     * Outputs a html template from the theme's templates folder
     * @param array $vars Variables to pass to the template
     */
    public function output(array $vars = [])
    {
        echo $this->app->theme->getTemplate($this->name, $vars);
    }
}
