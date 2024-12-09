<?php
/**
* The Document Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Document\Css;
use Mars\Document\Javascript;
use Mars\Document\Fonts;
use Mars\Document\Images;
use Mars\Document\Meta;
use Mars\Document\Rss;
use Mars\Document\Title;
use Mars\Lazyload\GhostTrait;

/**
 * The Device Class
 * Encapsulates the user's device
 */
class Document
{
    use InstanceTrait;
    use GhostTrait;

    /**
     * @var Css $css The css object
     */
    #[LazyLoad]
    public Css $css;

    /**
     * @var Javascript $javascript The javascript object
     */
    #[LazyLoad]
    public Javascript $javascript;

    /**
     * @var Fonts $fonts The fonts object
     */
    #[LazyLoad]
    public Fonts $fonts;

    /**
     * @var Images $images The images object
     */
    #[LazyLoad]
    public Images $images;

    /**
     * @var Meta $meta The meta object
     */
    #[LazyLoad]
    public Meta $meta;

    /**
     * @var Rss $rss The rss object
     */
    #[LazyLoad]
    public Rss $rss;

    /**
     * @var Title $title The title object
     */
    #[LazyLoad]
    public Title $title;

    /**
     * Builds the device object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;
    }
}
