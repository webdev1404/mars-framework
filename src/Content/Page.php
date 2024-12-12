<?php
/**
* The Page Content Class
* @package Mars
*/

namespace Mars\Content;

use Mars\App;

/**
 * The Page Content Class
 * Outputs the content of a page from app/pages
 */
class Page extends Content implements ContentInterface
{        
    public function output()
    {
        $this->outputTitleAndMeta();

        $template = $this->app->base_path . '/app/pages/' . $this->name . '.' . App::FILE_EXTENSIONS['templates'];

        echo $this->app->theme->getTemplateFromFilename($template);
    }
}
