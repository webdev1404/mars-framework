<?php
/**
* The System's Libraries Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\LazyLoadProperty;
use Mars\Extensions\Libraries\CssLibraries as Css;
use Mars\Extensions\Libraries\JavascriptLibraries as Javascript;

/**
 * The System's Libraries Class
 */
class Libraries
{
    use Kernel;
    use LazyLoad;

    /**
     * @var Css $css The CSS libraries handler
     */
    #[LazyLoadProperty]
    public Css $css;

    /**
     * @var Javascript $js The Javascript libraries handler
     */
    #[LazyLoadProperty]
    public Javascript $js;


    /**
     * Builds the Libraries instance
     * @param App $app The application instance
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->lazyLoad($this->app);
    }
}
