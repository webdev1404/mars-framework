<?php
/**
* The Document Class
* @package Mars
*/

namespace Mars;

use Mars\App\InstanceTrait;
use Mars\Document\Css;
use Mars\Document\Javascript;
use Mars\Document\Preload;
use Mars\Document\Prefetch;
use Mars\Document\Preconnect;
use Mars\Document\Fonts;
use Mars\Document\Images;
use Mars\Document\Meta;
use Mars\Document\Rss;
use Mars\Document\Encoding;
use Mars\Document\Favicon;
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
     * @var Preload $preload The preload object
     */
    #[LazyLoad]
    public Preload $preload;

    /**
     * @var Prefetch $prefetch The prefetch object
     */
    #[LazyLoad]
    public Prefetch $prefetch;

    /**
     * @var Preconnect $preconnect The preconnect object
     */
    #[LazyLoad]
    public Preconnect $preconnect;

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
     * @var Encoding $encoding The encoding object
     */
    #[LazyLoad]
    public Encoding $encoding;

    /**
     * @var Favicon $favicon The favicon object
     */
    #[LazyLoad]
    public Favicon $favicon;    

    /**
     * @var array $urls_list The list with the items which have urls to be outputted
     */
    protected array $urls_list = ['css', 'javascript'];

    /**
     * Builds the device object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        $this->lazyLoad($app);

        $this->app = $app;
    }

    /**
     * Outputs the required head tags
     */
    public function outputHead()
    {        
        $this->title->output();
        $this->encoding->output();
        $this->favicon->output();
        $this->meta->output();
        $this->rss->output();

        $this->preload->output();
        $this->prefetch->output();
        $this->preconnect->output();
        
        $this->outputUrls('head');
    }

    /**
     * Outputs the required footer tags
     */
    public function outputFooter()
    {
        $this->outputUrls('footer');
    }

    /**
     * Outputs the urls
     * @param string $location The location of the url [head|footer]
     */
    protected function outputUrls(string $location)
    {
        foreach ($this->urls_list as $name) {
            $this->$name->output($location);
        }
    }
}
