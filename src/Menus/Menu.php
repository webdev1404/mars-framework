<?php
/**
* The Menu Class
* @package Mars
*/

namespace Mars\Menus;

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
     * Get the Menu Item's ID
     * @param string $title The Menu Item's Title
     * @param string $url The Menu Item's URL
     * @return string The Menu Item's ID
     */
    public function getId(string $title, string $url) : string
    {
        return md5($title . $url);
    }

    /**
     * Add a Menu Item
     * @param string $title The Menu Item's Title
     * @param string $url The Menu Item's URL
     * @param string $id The Menu Item's ID
     * @param string $parent The Parent Menu Item's ID
     * @param int $priority The Menu Item's Priority
     * @param array $attributes The Menu Item's Attributes
     * @return static
     */
    public function add(string|array $title, string $url = '', string $id = '', string $parent = '', int $priority = 100, array $attributes = []) : static
    {
        if (is_array($title)) {
            return $this->addItems($title, $parent, $priority, $attributes);
        }

        return $this->addItem($title, $url, $id, $parent, $priority, $attributes);
    }

    /**
     * Add a Menu Item
     * @param string $title The Menu Item's Title
     * @param string $url The Menu Item's URL
     * @param string $id The Menu Item's ID
     * @param string $parent The Parent Menu Item's ID
     * @param int $priority The Menu Item's Priority
     * @param array $attributes The Menu Item's Attributes
     * @return static
     */
    public function addItem(string $title, string $url, string $id = '', string $parent = '', int $priority = 100, array $attributes = []) : static
    {
        if (!$id) {
            $id = $this->getId($title, $url);
        }
        if ($parent) {
            if (!isset($this->items[$parent])) {
                throw new \Exception("Adding menu failed. Parent Menu Item with ID '{$parent}' does not exist.");
            }
        }

        $this->items[$id] = [
            'title' => $title,
            'url' => $url,
            'parent' => $parent,
            'priority' => $priority,
            'attributes' => $attributes
        ];

        return $this;
    }

    /**
     * Add Multiple Menu Items
     * @param array $items The Menu Items to Add
     * @param string $parent The Parent Menu Item's ID
     * @param int $priority The Menu Items' Priority
     * @param array $attributes The Menu Items' Attributes
     * @return static
     */
    public function addItems(array $items, string $parent = '', int $priority = 100, array $attributes = []) : static
    {
        foreach ($items as $id => $item) {
            $title = $item[0] ?? '';
            $url = $item[1] ?? '';
            $item_children = $item[2] ?? [];

            $this->addItem($title, $url, $id, $parent, $priority, $attributes);

            if ($item_children) {
                $this->addItems($item_children, $id, $priority, $attributes);
            }
        }

        return $this;
    }

    /**
     * Update a Menu Item
     * @param string $id The Menu Item's ID
     * @param string $title The Menu Item's Title
     * @param string $url The Menu Item's URL
     * @param string $parent The Parent Menu Item's ID
     * @param int $priority The Menu Item's Priority
     * @param array $attributes The Menu Item's Attributes
     * @return static
     */
    public function update(string $id, string $title = '', string $url = '', string $parent = '', int $priority = 0, array $attributes = []) : static
    {
        if (!isset($this->items[$id])) {
            throw new \Exception("Updating menu failed. Menu Item with ID '{$id}' does not exist.");
        }

        $item = $this->items[$id];
        $title = $title ?: $item['title'];
        $url = $url ?: $item['url'];
        $parent = $parent ?: $item['parent'];
        $priority = $priority ?: $item['priority'];
        $attributes = $attributes ?: $item['attributes'];

        $this->addItem($title, $url, $id, $parent, $priority, $attributes);

        return $this;
    }

    /**
     * Remove a Menu Item
     * @param string $id The Menu Item's ID
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
     * Output the Menu
     */
    public function output()
    {
        $html = '';

        if ($this->type) {
            $key = "menu-{$this->type}-{$this->app->lang->name}";

            $html = $this->app->cache->data->get($key);
            if ($this->app->development) {
                $html = '';
            }
        }

        if (!$html) {
            $this->collectItems();

            $items = $this->getItems();

            $html = $this->getHtml($items);

            if ($this->type) {
                $this->app->cache->data->set($key, $html);
            }
        }

        echo $html;
    }

    /**
     * Returns the Menu's HTML
     * @param array $items The Menu Items to Output
     * @return string The Menu's HTML
     */
    protected function getHtml(array $items) : string
    {
        ob_start();

        echo '<ul>';
        foreach ($items as $id => $item) {
            $item['url'] = $this->getUrl($item['url']);
            $item['attributes']['class'] = $this->getClass($id, $item);

            echo '<li>';
            echo $this->app->html->a($item['url'], $item['title'], $item['attributes']);
            if (!empty($item['items'])) {
                echo $this->getHtml($item['items']);
            }
            echo '</li>';
        }
        echo '</ul>';

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
     * Collects the Menu Items
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

        if ($this->app->url->isValid($url)) {
            return $url;
        }

        return $this->app->url->route($url) ?? '#';
    }

    /**
     * Returns the Menu Item's Class
     * @param string $id The Menu Item's ID
     * @param array $item The Menu Item
     * @return string The Menu Item's Class
     */
    protected function getClass(string $id, array $item) : string
    {
        $is_active = false;
        if ($this->app->router->name && $this->app->router->name == $id) {
            $is_active = true;
        } elseif ($item['url'] == $this->app->url) {
            $is_active = true;
        }

        $class = $item['attributes']['class'] ?? '';
        if ($is_active) {
            $class .= ' ' . $this->classes['active'];
        }

        return trim($class);
    }
}
