<?php
/**
* The Pagination Class
* @package Mars
*/

namespace Mars\Ui\Pagination;

use Mars\App;
use Mars\App\Kernel;

/**
 * The Pagination Class
 * Generates pagination links
 */
class Pagination
{
    use Kernel;

    /**
     * @var string $base_url The generic base_url where the number of the page will be appended
     */
    public protected(set) string $base_url = '';

    /**
     * @var int $current_page The current page
     */
    public protected(set) int $current_page {
        get {
            if (isset($this->current_page)) {
                return $this->current_page;
            }

            $this->current_page = $this->app->request->getPage();
            if ($this->current_page <= 0 || $this->current_page > $this->total_pages) {
                $this->current_page = 1;
            }

            return $this->current_page;
        }
    }

    /**
     * @var int $total_pages The total number of pages
     */
    public protected(set) int $total_pages {
        get {
            if (isset($this->total_pages)) {
                return $this->total_pages;
            }

            $this->total_pages = ceil($this->total_items / $this->items_per_page);

            return $this->total_pages;
        }
    }

    /**
     * @var int $total_items The total number of items
     */
    public protected(set) int $total_items = 0;

    /**
     * @var int $items_per_page The number of items that should be displayed on each page
     */
    public protected(set) int $items_per_page = 0;

    /**
     * @var int $max_links The max number of pagination links to show
     */
    public protected(set) int $max_links = 0;

    /**
     * @var bool $seo_url If true, will use the seo param in the base url rather than append the page as a query param
     */
    public protected(set) bool $seo_url {
        get {
            if (isset($this->seo_url)) {
                return $this->seo_url;
            }

            $this->seo_url = str_contains($this->base_url, $this->seo_param);

            return $this->seo_url;
        }
    }

    /**
     * @var string $seo_param The string found in $base_url which will be replaced by the page number
     */
    public protected(set) string $seo_param = '';

    /**
     * Builds the pagination object
     * @param string $base_url The generic base_url where the number of the page will be appended
     * @param int $total_items The total numbers of items
     * @param bool $seo_url If true, will use the seo param in the base url rather than append the page as a query param
     * @param string $seo_param The string found in $base_url which will be replaced
     * @param int $items_per_page The number of items per page
     * @param int $max_links The max number of links to show
     * @param App $app The app object
     */
    public function __construct(string $base_url, int $total_items, bool $seo_url, string $seo_param, ?int $items_per_page = null, ?int $max_links = null, ?App $app = null)
    {
        $this->base_url = $base_url;
        $this->total_items = $total_items;
        $this->seo_url = $seo_url;
        $this->seo_param = $seo_param;
        $this->items_per_page = $items_per_page ?? $this->app->config->pagination->items_per_page;
        $this->max_links = $max_links ?? $this->app->config->pagination->max_links;
        $this->app = $app;
    }

    /**
     * Renders the pagination links
     */
    public function render()
    {
        if (!$this->total_items || $this->items_per_page > $this->total_items) {
            return;
        }

        $links = $this->getLinks();
        
        $this->app->theme->render('ui/pagination', ['links' => $links]);
    }

    /**
     * Returns the pagination links
     * @return array
     */
    public function getLinks() : array
    {
        [$start, $end] = $this->getLimits();

        $links = [];

        if ($this->current_page > 1) {
            $links['first'] = $this->getFirstLink();
            $links['previous'] = $this->getPreviousLink();
        }

        for ($i = $start; $i <= $end; $i++) {
            $class = ($i == $this->current_page) ? 'pagination-current' : '';

            $links['page-' . $i] = $this->getLink($i, $i, true, $class);
        }

        if ($this->current_page != $this->total_pages) {
            $links['next'] = $this->getNextLink();

            if ($this->total_pages > $this->max_links) {
                $links['last'] = $this->getLastLink();
            }
        }

        return $this->app->plugins->filter('ui.pagination.links', $links, $this);
    }

    /**
     * Determines the pages interval which should be displayed/are visible
     * @return array The start & end pages
     */
    protected function getLimits() : array
    {
        $start = 1;
        $end = 1;

        if ($this->max_links && $this->max_links < $this->total_pages) {
            $visible_links = floor($this->max_links / 2);
            $start = $this->current_page - $visible_links;
            $end = $this->current_page + $visible_links;

            if (!($this->max_links % 2)) {
                $start++;
            }
            if ($start <= 0) {
                $start = 1;
                $end = $this->max_links;
            } elseif ($end > $this->total_pages) {
                $end = $this->total_pages;
                $start = $end - $this->max_links + 1;
            }
        } else {
            $start = 1;
            $end = $this->total_pages;
        }

        return [$start, $end];
    }

    /**
     * Builds the url, by appending the page param
     * @param int $page The page number
     * @return string The url
     */
    protected function getUrl(int $page) : string
    {
        if ($this->seo_url) {
            //replace the seo page param with the page number
            return str_replace($this->seo_param, $page, $this->base_url);
        } else {
            //build the url, by appending the page as a query string
            return $this->app->url->add($this->base_url, [$this->app->config->request->page->param => $page]);
        }
    }

    /**
     * Returns the link array
     * @param int $page The page number
     * @param string $title The link's title
     * @param string $class The class of the link, if any
     * @return array
     */
    protected function getLink(int $page, string $title, string $class = '') : array
    {
        return ['title' => $title, 'url' => $this->getUrl($page), 'page' => $page, 'class' => $class];
    }

    /**
     * Returns the data for the first link
     * @return array
     */
    protected function getFirstLink() : array
    {
        return $this->getLink(1, App::__('pagination.first'), 'pagination-first');
    }

    /**
     * Returns the data for the last link
     * @return array
     */
    protected function getLastLink() : array
    {
        return $this->getLink($this->total_pages, App::__('pagination.last'), 'pagination-last');
    }

    /**
     * Returns the data for the previous link
     * @internal
     */
    protected function getPreviousLink() : array
    {
        return $this->getLink($this->current_page - 1, App::__('pagination.previous'),  'pagination-previous');
    }

    /**
     * Returns the data for the next link
     * @internal
     */
    protected function getNextLink() : array
    {
        return $this->getLink($this->current_page + 1, App::__('pagination.next'), 'pagination-next');
    }
}
