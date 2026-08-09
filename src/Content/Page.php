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
    public function run(string $action = '', array $vars = []) : Data
    {
        $filename = $this->data['filename'];

        if (!str_starts_with($filename, '/')) {
            $filename = $this->app->app_path . '/pages/' . $filename . '.php';
        }

        $response = $this->app->response->body->create(null, $this->app->theme->getTemplateByFilename($filename, vars: $vars));

        $this->setTitle();
        $this->setBreadcrumbs();

        return $response;
    }

    /**
     * Sets the title for the page
     */
    protected function setTitle()
    {
        if ($this->app->document->title->value) {
            //do nothing if the title has already been set in the page template
            return;
        }

        $title = $this->app->file->getStem($this->data['file']);
        if ($title == 'index') {
            $parts = explode('/', $this->data['file']);
            array_pop($parts);

            $title = array_last($parts);
        }

        $title = $this->app->document->title->format($title);

        $this->app->document->title->set($title);
    }

    /**
     * Sets the breadcrumbs for the page
     */
    protected function setBreadcrumbs()
    {
        if ($this->app->ui->breadcrumbs->breadcrumbs) {
            //do nothing if the breadcrumbs have already been set in the page template
            return;
        }

        $this->app->ui->breadcrumbs->generate($this->app->router->route);
    }
}
