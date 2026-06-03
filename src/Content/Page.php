<?php
/**
* The Page Content Class
* @package Mars
*/

namespace Mars\Content;

use Mars\Http\Response\Body\Data\Data;

/**
 * The Page Content Class
 * Outputs the content of a page from app/pages
 */
class Page extends Content implements ContentInterface
{
    /**
     * @see ContentInterface::run()
     * {@inheritDoc}
     */
    public function run(array $vars = []) : Data
    {
        $filename = $this->name;
        
        if (!str_starts_with($filename, '/')) {
            $filename = $this->app->app_path . '/pages/' . $this->name . '.php';
        }

        return $this->app->response->body->create(null, $this->app->theme->getTemplateByFilename($filename, vars: $vars));
    }
}
