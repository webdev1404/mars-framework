<?php
/**
* The User Interface (UI) Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Handlers;

/**
 * The User Interface (UI) Class
 */
class Ui
{
    use Kernel;

    /**
     * @var array $supported_rules The list of supported rules
     */
    protected array $supported_uis = [
        'pagination' => \Mars\Ui\Pagination::class
    ];

    /**
     * @var Handlers $handlers The handlers object
     */
    public protected(set) Handlers $uis {
        get {
            if (isset($this->uis)) {
                return $this->uis;
            }
    
            $this->uis = new Handlers($this->supported_uis, null, $this->app);
    
            return $this->uis;
        }
    }

    /**
     * Builds pagination. The number of pages is computed as $total_items / $items_per_page.
     * @param string $base_url The generic base_url where the number of the page will be appended
     * @param int $total_items The total numbers of items
     * @param int $items_per_page The number of items per page
     * @param int $max_links The max number of links to show
     * @return string The html code of the pagination
     */
    public function buildPagination(string $base_url, int $total_items, ?int $items_per_page = null, ?int $max_links = null) : string
    {
        $items_per_page = $items_per_page ?? $this->app->config->pagination_items_per_page;
        $max_links = $max_links ?? $this->app->config->pagination_max_links;

        $pag = $this->uis->get('pagination', $base_url, $items_per_page, $total_items, $max_links);

        return $pag->get();
    }
}
