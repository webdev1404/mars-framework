<?php
/**
* The Pagination Class
* @package Mars
*/

namespace Mars\Ui;

use Mars\Ui\Pagination\Pagination as PaginationObj;

/**
 * The Pagination Class
 * Generates pagination links
 */
class Pagination extends Ui
{
    /**
     * Renders the pagination links
     * @param string $base_url The generic base_url where the number of the page will be appended
     * @param int $total_items The total numbers of items
     * @param bool $seo_url If true, will use the seo param in the base, for nice urls, rather than append the page as a query param
     * @param string $seo_param The string found in $base_url which will be replaced by the page number
     * @param int $items_per_page The number of items per page. If null, will use the default value from the config
     * @param int $max_links The max number of links to show. If null, will use the default value from the config
     */
    public function render(string $base_url, int $total_items, bool $seo_url = false, string $seo_param = '{PAGE_NO}', ?int $items_per_page = null, ?int $max_links = null)
    {
        $pagination = new PaginationObj($base_url, $total_items, $seo_url, $seo_param, $items_per_page, $max_links, $this->app);
        $pagination->render();
    }
}
