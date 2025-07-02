<?php
/**
* The Page Content Class
* @package Mars
*/

namespace Mars\Content;

/**
 * The Page Content Class
 * Outputs the content of a page from app/pages
 */
class Page extends Content implements ContentInterface
{
    /**
     * Outputs a html page from the app's pages folder
     */
    public function output()
    {
        $this->outputTitleAndMeta();

        $template = $this->app->base_path . '/app/pages/' . $this->name . '.php';

        echo $this->app->theme->getTemplateFromFilename($template);
    }
}
