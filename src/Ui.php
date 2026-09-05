<?php
/**
* The User Interface (UI) Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Ui\Menus;
use Mars\Ui\Breadcrumbs;
use Mars\Ui\Pagination;

/**
 * The User Interface (UI) Class
 */
class Ui
{
    use Kernel;
    use LazyLoad;

    /**
     * @var Menus $menus The menus object
     */
    #[LazyLoadProperty]
    public Menus $menus;

    /**
     * @var Breadcrumbs $breadcrumbs The breadcrumbs object
     */
    #[LazyLoadProperty]
    public Breadcrumbs $breadcrumbs;

    /**
     * @var Pagination $pagination The pagination object
     */
    #[LazyLoadProperty]
    public Pagination $pagination;

    /**
     * Builds the UI object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($this->app);

        $this->app = $app;
    }
}
