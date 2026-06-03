<?php
/**
 * The Theme's Fonts Links Class
 * @package Mars
 */

namespace Mars\Extensions\Themes\Links;

use Mars\Document\Link\Links as DocumentLinks;
use Mars\Extensions\Themes\Theme;

/**
 * The Theme's Fonts Links Class
 * @package Mars
 */
class Fonts extends Links
{
    /**
     * @internal
     */
    public DocumentLinks $urls {
        get => $this->app->document->fonts;
    }

    /**
     * @internal
     */
    protected string $assets_dir {
        get => Theme::DIRS['fonts'];
    }
}
