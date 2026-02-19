<?php
/**
* The Document Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\LazyLoad;
use Mars\App\LazyLoadProperty;
use Mars\Document\Links\Css;
use Mars\Document\Links\Javascript;
use Mars\Document\Links\Fonts;
use Mars\Document\Links\Images;
use Mars\Document\Hints\Preload;
use Mars\Document\Hints\Prefetch;
use Mars\Document\Hints\Preconnect;
use Mars\Document\Tags\Meta;
use Mars\Document\Tags\Rss;
use Mars\Document\Tags\Encoding;
use Mars\Document\Tags\Favicon;
use Mars\Document\Tags\Title;

/**
 * The Document Class
 * Encapsulates the html document
 */
class Document
{
    use Kernel;
    use LazyLoad;

    /**
     * @var Css $css The css object
     */
    #[LazyLoadProperty]
    public Css $css;

    /**
     * @var Javascript $js The javascript object
     */
    #[LazyLoadProperty]
    public Javascript $js;

    /**
     * @var Fonts $fonts The fonts object
     */
    #[LazyLoadProperty]
    public Fonts $fonts;

    /**
     * @var Images $images The images object
     */
    #[LazyLoadProperty]
    public Images $images;

    /**
     * @var Preload $preload The preload object
     */
    #[LazyLoadProperty]
    public Preload $preload;

    /**
     * @var Prefetch $prefetch The prefetch object
     */
    #[LazyLoadProperty]
    public Prefetch $prefetch;

    /**
     * @var Preconnect $preconnect The preconnect object
     */
    #[LazyLoadProperty]
    public Preconnect $preconnect;

    /**
     * @var Meta $meta The meta object
     */
    #[LazyLoadProperty]
    public Meta $meta;

    /**
     * @var Rss $rss The rss object
     */
    #[LazyLoadProperty]
    public Rss $rss;

    /**
     * @var Title $title The title object
     */
    #[LazyLoadProperty]
    public Title $title;

    /**
     * @var Encoding $encoding The encoding object
     */
    #[LazyLoadProperty]
    public Encoding $encoding;

    /**
     * @var Favicon $favicon The favicon object
     */
    #[LazyLoadProperty]
    public Favicon $favicon;

    /**
     * Builds the document object
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
        $this->css->output($location);
        $this->js->output($location);
    }
}
