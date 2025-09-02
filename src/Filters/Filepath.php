<?php
/**
* The Filepath Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Filepath Filter Class
 */
class Filepath extends Filename
{
    /**
     * @see \Mars\Filters\Filter::filepath()
     */
    public function filter(string $filepath) : string
    {
        $dir = $this->app->file->getDir($filepath);
        $filename = basename($filepath);

        if ($dir) {
            $filepath = $dir . '/' . parent::filter($filename);
        } else {
            $filepath = parent::filter($filename);
        }

        return $this->app->plugins->filter('filters_filepath_filter', $filepath);
    }
}
