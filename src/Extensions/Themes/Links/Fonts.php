<?php
/**
 * The Theme's Fonts Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Links\Urls as DocumentUrls;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Fonts Links Class
 * @package Mars
 */
class Fonts extends Urls
{
    /**
     * @internal
     */
    protected DocumentUrls $urls {
        get => $this->app->document->fonts;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['fonts'];
    }
}
