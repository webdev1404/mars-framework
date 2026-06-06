<?php
/**
 * The Theme's Css Urls Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Links as DocumentLinks;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Css Links Class
 * @package Mars
 */
class Css extends Links
{
    /**
     * @internal
     */
    public DocumentLinks $urls {
        get => $this->app->document->css;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['css'];
    }
}
