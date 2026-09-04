<?php
/**
* The Menu Class
* @package Mars
*/

namespace Mars\Ui\Menus;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Menu Class
 */
class Menu
{
    use Kernel;

    /**
     * @var array $classes The Menu's Classes
     */
    public array $classes = [
        'active' => 'active'
    ];

    /**
     * @var array $items The Menu's Items
     */
    public protected(set) array $items = [];

    /**
     * @var string $type The Menu's Type
     */
    public protected(set) string $type = '';

    /**
     * @var bool $can_cache Whether to use cache or not
     */
    public bool $can_cache = true;

    /**
     * Menu Constructor
     * @param string $type The Menu's Type
     * @param App $app The App Instance
     */
    public function __construct(string $type, ?App $app = null)
    {
        $this->app = $app;
        $this->type = $type;
    }

    /**
     * Add a Menu Item
     * @param string $title The Menu's Title
     * @param string $url The Menu's URL
     * @param string $id The Menu's ID
     * @param string $parent The Parent Menu Item's ID
     * @param string $content The Menu's Content
     * @param int $priority The Menu's Priority
     * @param array $attributes The Menu's Attributes
     * @return static
     */
    public function add(string|array $title, string $url = '', string $id = '', string $parent = '', string $content = '', int $priority = 100, array $attributes = []) : static
    {
        if (is_array($title)) {
            return $this->addItems($title, $parent, $priority, $attributes);
        }

        return $this->addItem($title, $url, $id, $parent, $content, $priority, $attributes);
    }

    /**
     * Add a Menu Item
     * @param string $title The Menu's Title
     * @param string $url The Menu's URL
     * @param string $id The Menu's ID
     * @param string $parent The Parent Menu Item's ID
     * @param string $content The Menu's Content
     * @param int $priority The Menu's Priority
     * @param array $attributes The Menu's Attributes
     * @return static
     */
    public function addItem(string $title, string $url, string $id = '', string $parent = '', string $content = '', int $priority = 100, array $attributes = []) : static
    {
        if ($parent) {
            if (!isset($this->items[$parent])) {
                throw new \Exception("Adding menu failed. Parent Menu Item with ID '{$parent}' does not exist.");
            }
        }

        $url = $this->getUrl($url);
        $id = $this->app->id->get($id, $title . $url);
        $attributes = $this->getAttributes($url, $id, $attributes);

        $this->items[$id] = [
            'title' => $title,
            'url' => $this->app->url->get($url),
            'id' => $id,
            'parent' => $parent,
            'content' => $content,
            'priority' => $priority,
            'attributes' => $attributes
        ];

        return $this;
    }

    /**
     * Add Multiple Menu Items
     * @param array $items The Menus to Add
     * @param string $parent The Parent Menu Item's ID
     * @param int $priority The Menus' Priority
     * @param array $attributes The Menus' Attributes
     * @return static
     */
    public function addItems(array $items, string $parent = '', int $priority = 100, array $attributes = []) : static
    {
        foreach ($items as $id => $item) {
            $title = $item[0] ?? '';
            $url = $item[1] ?? '';
            $item_children = $item[2] ?? [];
            $content = $item[3] ?? '';

            $this->addItem($title, $url, $id, $parent, $content, $priority, $attributes);

            if ($item_children) {
                $this->addItems($item_children, $id, $priority, $attributes);
            }
        }

        return $this;
    }

    /**
     * Update a Menu Item
     * @param string $id The Menu's ID
     * @param string $title The Menu's Title
     * @param string $url The Menu's URL
     * @param string $parent The Parent Menu Item's ID
     * @param string $content The Menu's Content
     * @param int $priority The Menu's Priority
     * @param array $attributes The Menu's Attributes
     * @return static
     */
    public function update(string $id, string $title = '', string $url = '', string $parent = '', string $content = '', int $priority = 0, array $attributes = []) : static
    {
        if (!isset($this->items[$id])) {
            throw new \Exception("Updating menu failed. Menu Item with ID '{$id}' does not exist.");
        }

        $item = $this->items[$id];
        $title = $title ?: $item['title'];
        $url = $url ?: $item['url'];
        $parent = $parent ?: $item['parent'];
        $content = $content ?: $item['content'];
        $priority = $priority ?: $item['priority'];
        $attributes = $attributes ?: $item['attributes'];

        $this->addItem($title, $url, $id, $parent, $content, $priority, $attributes);

        return $this;
    }

    /**
     * Remove a Menu Item
     * @param string $id The Menu's ID
     * @return static
     */
    public function remove(string $id) : static
    {
        unset($this->items[$id]);

        return $this;
    }

    /**
     * Copies all menu items from another menu
     * @param Menu $menu The Menu to Copy From
     * @return static
     */
    public function copy(Menu $menu) : static
    {
        $this->items = $menu->items;

        return $this;
    }

    /**
     * Reset the Menu
     * @return static
     */
    public function reset() : static
    {
        $this->items = [];

        return $this;
    }

    /**
     * Renders the Menu
     */
    public function render()
    {
        $html = '';

        if ($this->type && $this->can_cache) {
            $key = "menu-{$this->type}-{$this->app->lang->name}";

            $html = $this->app->cache->data->get($key);
            if ($this->app->development) {
                $html = '';
            }
        }

        if (!$html) {
            $this->collectItems();

            $items = $this->getItems();

            $items = $this->app->plugins->filter('ui.menu.items', $items, $this);

            $html = $this->getHtml($items);

            if ($this->type && $this->can_cache) {
                $this->app->cache->data->set($key, $html);
            }
        }

        echo $html;
    }

    /**
     * Returns the Menu's HTML
     * @param array $items The Menus to render
     * @return string The Menu's HTML
     */
    protected function getHtml(array $items) : string
    {
        ob_start();

        $this->app->theme->render('ui/menu', ['items' => $items, 'type' => $this->type]);

        return ob_get_clean();
    }

    /**
     * Returns the Menu's Items, sorted by priority
     * @return array The Menu's Items
     */
    protected function getItems(string $parent = '') : array
    {
        $items = [];
        foreach ($this->items as $id => $item) {
            if ($item['parent'] != $parent) {
                continue;
            }

            $item['items'] = $this->getItems($id);
            $items[$id] = $item;

            unset($this->items[$id]);
        }

        //sort the items by priority
        uasort($items, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $items;
    }

    /**
     * Collects The Menus
     * This method can be overridden in child classes to collect menu items from different sources
     */
    protected function collectItems()
    {
    }

    /**
     * Returns the url
     * @param string $url The URL to Get
     * @return string The URL
     */
    protected function getUrl(string $url) : string
    {
        $allowed = ['#', 'javascript:void(0)'];
        if (in_array($url, $allowed)) {
            return $url;
        }

        return $this->app->url->get($url);
    }

    /**
     * Returns The Menu's Attributes
     * @param string $url The Menu's URL
     * @param string $id The Menu's ID
     * @param array $attributes The Menu's Attributes
     * @return array The Menu's Attributes
     */
    protected function getAttributes(string $url, string $id, array $attributes) : array
    {
        $is_active = false;
        if ($this->app->router->name && $this->app->router->name == $id) {
            $is_active = true;
        } elseif ($url == $this->app->url) {
            $is_active = true;
        }

        $class = $attributes['class'] ?? '';
        if ($is_active) {
            $class .= ' ' . $this->classes['active'];
        }

        $attributes['class'] = trim($class);

        return $attributes;
    }
}
