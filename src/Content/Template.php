<?php
/**
* The Template Content Class
* @package Mars
*/

namespace Mars\Content;

use Mars\Http\Response\Body\Data\Data;

/**
 * The Template Content Class
 * Outputs the content of a template
 */
class Template extends Content implements ContentInterface
{
    /**
     * @see ContentInterface::run()
     * {@inheritDoc}
     */
    public function run(array $vars = []) : Data
    {
        return $this->app->response->body->create(null, $this->app->theme->getTemplate($this->name, $vars));
    }
}
