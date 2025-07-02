<?php
/**
 * The Theme's Css Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls;
use Mars\Extensions\Theme;

/**
 * The Theme's Css Links Class
 * @package Mars
 */
class Css extends Url
{
    /**
     * @internal
     */
    protected Urls $url {
        get => $this->app->document->css;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['css'];
    }
}