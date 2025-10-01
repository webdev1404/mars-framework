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
        $filename = $this->name;
        
        if (!str_starts_with($filename, '/')) {
            $filename = $this->app->app_path . '/pages/' . $this->name . '.php';
        }

        echo $this->app->theme->getTemplateFromFilename($filename);
    }
}
