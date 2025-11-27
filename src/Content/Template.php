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
    public function output(array $params = [])
    {
        echo $this->app->theme->getTemplate($this->name);
    }
}
