<?php
/**
* The Article Content Class
* @package Mars
*/

namespace Mars\Content;

use Mars\Http\Response\Body\Data\Data;

/**
 * The Article Content Class
 * Outputs the content of an article from app/articles
 */
class Article extends Content implements ContentInterface
{
    /**
     * @see ContentInterface::run()
     * {@inheritDoc}
     */
    public function run(string $action = '', array $vars = []) : Data
    {
        $filename = $this->data['filename'];

        if (!str_starts_with($filename, '/')) {
            $filename = $this->app->app_path . '/articles/' . $filename . '.php';
        }

        $response = $this->app->response->body->create(null, $this->app->theme->getTemplateByFilename($filename, vars: $vars));

        $this->setTitle();
        $this->setBreadcrumbs();

        return $response;
    }

    /**
     * Sets the title for the article
     */
    protected function setTitle()
    {
        if ($this->app->document->title->value) {
            //do nothing if the title has already been set in the article template
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
     * Sets the breadcrumbs for the article
     */
    protected function setBreadcrumbs()
    {
        if ($this->app->ui->breadcrumbs->breadcrumbs) {
            //do nothing if the breadcrumbs have already been set in the article template
            return;
        }

        $this->app->ui->breadcrumbs->generate($this->app->router->route);
    }
}
