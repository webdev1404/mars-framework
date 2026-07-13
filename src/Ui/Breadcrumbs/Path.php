<?php
/**
* The Path Breadcrumbs Class
* @package Mars
*/

namespace Mars\Ui\Breadcrumbs;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Path Breadcrumbs Class
 * Generates breadcrumbs based on a path
 */
class Path
{
    use Kernel;

    /**
     * Generates breadcrumbs based on a path
     * @param string $path The path to generate breadcrumbs from
     * @return array The generated breadcrumbs
     */
    public function generate(string $path) : array
    {
        $breadcrumbs = [];

        $parts = explode('/', $path);
        $last = array_pop($parts);

        foreach ($parts as $i => $part) {
            $title = $this->app->document->title->format($part);
            $route = implode('/', array_slice($parts, 0, $i + 1));

            $breadcrumbs[$title] = (string)$this->app->url->get($route);
        }

        //add the title of the document as the last breadcrumb, if it exists, otherwise use the last part of the path
        $title = '';
        if ($this->app->document->title->value) {
            $title = $this->app->document->title->value;
        } else {
            $title = $this->app->document->title->format($last);
        }

        $breadcrumbs[$title] = $title;

        return $breadcrumbs;
    }
}

