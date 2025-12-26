<?php
/**
 * The Theme's Css Urls Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls as DocumentUrls;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Css Urls Class
 * @package Mars
 */
class Css extends Urls
{
    /**
     * @internal
     */
    protected DocumentUrls $urls {
        get => $this->app->document->css;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['css'];
    }
}
