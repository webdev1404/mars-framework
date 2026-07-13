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
    use MapTrait {
        set as setMap;
    }

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
     * Sets a breadcrumb
     * @param string|array $name The name of the breadcrumb or an array of breadcrumbs
     * @param string $value The value of the breadcrumb, if $name is a string
     * @return static
     */
    public function set(string|array $name, mixed $value = '') : static
    {
        if (is_string($name)) {
            if (!$value) {
                $value = $name;
            }
        }

        return $this->setMap($name, $value);
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

        //save the last breadcrumb
        $last = array_pop($this->breadcrumbs);

        $breadcrumbs_list = array_map(fn($url) => (string)$this->app->url->get($url), $this->breadcrumbs);

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