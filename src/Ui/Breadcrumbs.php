<?php
/**
* The Breadcrumbs Class
* @package Mars
*/

namespace Mars\Ui;

use Mars\App\Handlers;
use Mars\Data\MapTrait;

/**
 * The Breadcrumbs Class
 * Renders breadcrumb links
 */
class Breadcrumbs extends Ui
{
    use MapTrait;

    /**
     * @var array $breadcrumbs The breadcrumbs
     */
    public protected(set) array $breadcrumbs = [];

    /**
     * @var string $property The property name
     */
    protected static string $property = 'breadcrumbs';

    /**
     * @var array $generators_list The list of supported generators
     */
    public protected(set) array $generators_list = [
        'path' => \Mars\Ui\Breadcrumbs\Path::class,
    ];

    /**
     * @var Handlers $generators The generators handlers
     */
    public protected(set) Handlers $generators {
        get {
            if (isset($this->generators)) {
                return $this->generators;
            }

            $this->generators = new Handlers($this->generators_list, null, $this->app);

            return $this->generators;
        }
    }

    /**
     * Renders the breadcrumbs
     */
    public function render()
    {
        if (!$this->breadcrumbs || !$this->app->config->breadcrumbs->show) {
            return;
        }

        $breadcrumbs = $this->getBreadcrumbs();

        $this->app->theme->render('ui/breadcrumbs', ['breadcrumbs' => $breadcrumbs]);
    }

    /**
     * Returns the breadcrumbs array
     * @return array The breadcrumbs
     */
    protected function getBreadcrumbs() : array
    {
        $breadcrumbs = [];

        if ($this->app->config->breadcrumbs->home) {
            $breadcrumbs[$this->app->config->breadcrumbs->home] = (string)$this->app->url->root;
        }

        $last = array_last($this->breadcrumbs);
        $last_key = array_key_last($this->breadcrumbs);
        if (!$last) {
            array_pop($this->breadcrumbs);

            $last = $last_key;
        } elseif (is_numeric($last_key)) {
            $last = array_pop($this->breadcrumbs);
        } else {
            $last = $this->app->document->title->value;
        }

        $breadcrumbs_list = array_map(fn ($url) => (string)$this->app->url->get($url), $this->breadcrumbs);

        $breadcrumbs = [...$breadcrumbs, ...$breadcrumbs_list, ...[$last => '']];

        return $this->app->plugins->filter('ui.breadcrumbs.list', $breadcrumbs, $this);
    }

    /**
     * Generate breadcrumbs
     * @param mixed $items The items to generate breadcrumbs from
     * @param string|null $handler The handler name
     */
    public function generate(mixed $items, ?string $handler = null)
    {
        $handler ??= $this->app->config->breadcrumbs->generator;

        $breadcrumbs = $this->generators->get($handler)->generate($items);

        $this->set($breadcrumbs);
    }
}
